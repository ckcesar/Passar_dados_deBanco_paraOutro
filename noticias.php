<!--Este exemplo pega todas as noticia de um banco e transferi para outro.-->
<!---->
<!--Lembrando que as imagens na transferência somente muda a url no banco,-->
<!--as imagens eu peguei direto no FTP e copiei e colei no local do novo endereço.-->

<?php

    set_time_limit(0);

    $pdo1 = new PDO("mysql:host=;dbname=", "usuario", "senha");
    $pdo1->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);

    $pdo2 = new PDO("mysql:host=;dbname=", "usuario", 'senha');
    $pdo2->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);

    $sql1 = "SELECT a.id_noticias AS cod, b.titulo, b.texto, b.dt_registro, CASE b.status WHEN 'A' THEN '1' WHEN 'I' THEN '0' END AS sit_doc
                FROM noticias a, documento b
                WHERE a.id_documento = b.id_documento";
    $smt1 = $pdo1->query($sql1);

    foreach($smt1 as $res){

            $insert_v = "INSERT INTO noticias (`id`, `titulo`, `subtitulo`, `conteudo`, `data_cadastro`, `data_vigor`, `data_final`, `situacao`)
	        VALUES (NULL, '{$res->titulo}', NULL, '{$res->texto}', '{$res->dt_registro}', NULL, NULL, '{$res->sit_doc}')";

            $pdo2->exec($insert_v);
            $id = $pdo2->lastInsertId("id");


        $sql_midia = "SELECT a.id_noticias, b.titulo, b.texto, b.dt_registro, CASE b.status WHEN 'A' THEN '1' WHEN 'I' THEN '0' END AS sit_doc,
                        CASE c.status WHEN 'A' THEN '1' WHEN 'I' THEN '0' END AS sit_midia, c.id_midia, c.link, c.link_miniatura, c.comentario, e.descricao,
                        CASE e.descricao WHEN 'FOTO' THEN 'F' WHEN 'Documento' THEN 'PDF' END AS tipo
                      FROM noticias a, documento b, midia c, mov_midia d, tp_midia e
                      WHERE a.id_documento = b.id_documento
                        AND c.id_midia = d.id_midia
                        AND b.id_documento = d.id_documento
                        AND c.id_tp_midia = e.id_tp_midia
                        AND a.id_noticias = '{$res->cod}'";
                        $result = $pdo1->query($sql_midia);

                        foreach($result as $row){

                            if($row->tipo === 'F'){
                                $img = str_replace('fotos/cmpeabir/','',$row->link);
                                $img_m = str_replace('fotos/cmpeabir/tumb/','',$row->link_miniatura);

                                $url = 'upload/imgs/_modulos/noticias/'.$img;
                                $url_mini = 'upload/imgs/_modulos/noticias_mini/'.$img_m;

                            }else if($row->tipo === 'PDF'){
                                $pdf = str_replace('arquivos/','',$row->link);
                                $url = 'upload/pdf/_modulos/noticias/'.$pdf;
                            }

                        $insert_m = "INSERT INTO midia(`id`, `titulo`, `descricao`, `tipo`, `url`, `url_mini`, `link`, `data_cadastro`, `situacao`, `destaque`, `ordem`)
                        VALUES(NULL, NULL, '{$row->comentario}', '{$row->tipo}', '{$url}', '{$url_mini}', NULL, '{$row->dt_registro}', '{$row->sit_midia}', '0', '0')";

                        $pdo2->exec($insert_m);
                        $id_midia = $pdo2->lastInsertId("id");

                        $insert_r = "INSERT INTO midia_rel(`data_cadastro`, `situacao`, `midia_id`, `produtos_id`, `itens_id`, `aplicacao_id`, `banners_id`, `vereadores_id`, `noticias_id`, `transparencia_id`, `area_id`)
                        VALUES('{$row->dt_registro}', '1', '{$id_midia}', NULL, NULL, NULL, NULL, NULL, '{$id}', NULL, NULL)";

                        $pdo2->exec($insert_r);


                        }

    }


