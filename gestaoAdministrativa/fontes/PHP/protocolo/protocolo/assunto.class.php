<?php
/*
    **********************************************************************************
    *                                                                                *
    * @package URBEM CNM - Soluções em Gestão Pública                                *
    * @copyright (c) 2013 Confederação Nacional de Municípos                         *
    * @author Confederação Nacional de Municípios                                    *
    *                                                                                *
    * O URBEM CNM é um software livre; você pode redistribuí-lo e/ou modificá-lo sob *
    * os  termos  da Licença Pública Geral GNU conforme  publicada  pela Fundação do *
    * Software Livre (FSF - Free Software Foundation); na versão 2 da Licença.       *
    *                                                                                *
    * Este  programa  é  distribuído  na  expectativa  de  que  seja  útil,   porém, *
    * SEM NENHUMA GARANTIA; nem mesmo a garantia implícita  de  COMERCIABILIDADE  OU *
    * ADEQUAÇÃO A UMA FINALIDADE ESPECÍFICA. Consulte a Licença Pública Geral do GNU *
    * para mais detalhes.                                                            *
    *                                                                                *
    * Você deve ter recebido uma cópia da Licença Pública Geral do GNU "LICENCA.txt" *
    * com  este  programa; se não, escreva para  a  Free  Software Foundation  Inc., *
    * no endereço 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.       *
    *                                                                                *
    **********************************************************************************
*/
?>
 <?php
/**
* Classe de negócio Assunto
* Data de Criação: 25/07/2005

* @author Analista: Cassiano
* @author Desenvolvedor: Cassiano

$Revision: 3819 $
$Name$
$Author: lizandro $
$Date: 2005-12-13 16:03:28 -0200 (Ter, 13 Dez 2005) $

Casos de uso: uc-01.06.95
*/
?>
<?php
    class assunto
    {
        /*** Declaração das variáveis ***/
            var $codigo;
            var $codClassificacao;
            var $nome;
            var $documento;
            var $tipo;
            var $ordem;
            var $sSQLListaTipoRelatorio;
            var $confidencial;

        /*** Método Construtor ***/
            function assunto()
            {
                $this->codigo = 0;
                $this->codClassificacao = 0;
                $this->nome = "";
                $this->documento = "";
                $this->tipo = "";
                $this->ordem = "";
                $this->sSQLListaTipoRelatorio = "";
                $this->confidencial = "";
            }

        /*** Método que seta Variáveis ***/
            function setaVariaveis($codClass, $nom, $conf)
            {
                $this->codClassificacao = $codClass;
                $this->nome = $nom;
                $this->confidencial = $conf;
            }

        /*** Método que lista a classificação ***/
            function listaClassificacao()
            {
                $dbConfig = new databaseLegado;
                $dbConfig->abreBd();
                $select =   "select cod_classificacao, nom_classificacao
                                from sw_classificacao
                                Order by nom_classificacao ";
                $dbConfig->abreSelecao($select);
                while (!$dbConfig->eof()) {
                    $cod = $dbConfig->pegaCampo("cod_classificacao");
                    $lista[$cod] = $dbConfig->pegaCampo("nom_classificacao");
                    $dbConfig->vaiProximo();
                }
                $dbConfig->limpaSelecao();

                return $lista;
                $dbConfig->fechaBd();
            }

        /*** Método que lista os documentos na tabela documentos ***/
            function listaDocumentos()
            {
                $dbConfig = new databaseLegado;
                $dbConfig->abreBd();
                $select =   "select cod_documento, nom_documento
                                from sw_documento order by nom_documento";
                $dbConfig->abreSelecao($select);
                while (!$dbConfig->eof()) {
                    $cod = $dbConfig->pegaCampo("cod_documento");
                    $lista[$cod] = $dbConfig->pegaCampo("nom_documento");
                    $dbConfig->vaiProximo();
                }
                $dbConfig->limpaSelecao;

                return $lista;
                $dbConfig->fechaBd();
            }

        /*** Método que inclui tipo de processo ***/
            function incluiAssunto()
            {
                $dbConfig = new databaseLegado;
                $dbConfig->abreBd();
                    if (isset($this->nome)) {
                        $insert =   "insert into sw_assunto (cod_assunto, cod_classificacao, nom_assunto, confidencial)
                                        values ($this->codigo, $this->codClassificacao, '$this->nome', '$this->confidencial')";
                                        //insere os dados na tabela sw_assunto
                        //echo $insert;
                        $result = $dbConfig->executaSql($insert);
                        if ($result) {
                            return true;
                            $dbConfig->fechaBd();
                        } else {
                            return false;
                            $dbConfig->fechaBd();
                        }
                    }
            }

        /*** Método que inclui os documentos no tipo de processo ***/
            function incluiDocumentos($cod1, $cod2, $doc)
            {
                $dbConfig = new databaseLegado;
                $dbConfig->abreBd();
               // reset($doc);
               $delete =   "delete from sw_documento_assunto
                            where cod_assunto = '$cod1'
                            and cod_classificacao = '$cod2'";
               //echo $delete."<br>";
               if ($dbConfig->executaSql($delete)) {
                 if (is_array($doc)) {
                    while (list ($key, $val) = each ($doc)) {
                            $insert =   "insert into sw_documento_assunto
                                            (cod_assunto, cod_classificacao, cod_documento)
                                            values ('".$cod1."', '".$cod2."', '".$val."')";
                            //echo $insert."<br>";
                            $dbConfig->executaSql($insert);
                    }
                 }
               }
               $dbConfig->fechaBd();
            }

        /*** Método que lista os tipos de processos existentes ***/
            function listaAssunto()
            {
                $dbConfig = new databaseLegado;
                $dbConfig->abreBd();
                $select =   "select cod_assunto, assunto.cod_classificacao, nom_assunto, assunto.confidencial,
                                classificacao.nom_classificacao
                                from sw_assunto as assunto, sw_classificacao as classificacao
                                where assunto.cod_classificacao = classificacao.cod_classificacao order by lower(nom_assunto)";
                $dbConfig->abreSelecao($select);
                while (!$dbConfig->eof()) {
                    $cod = $dbConfig->pegaCampo("cod_assunto").".".$dbConfig->pegaCampo("cod_classificacao");
                    $lista[$cod] = $dbConfig->pegaCampo("nom_assunto")."/".$dbConfig->pegaCampo("nom_classificacao")."/".
                    $dbConfig->pegaCampo("confidencial");
                    $dbConfig->vaiProximo();
                }
                $dbConfig->limpaSelecao();

                return $lista;
                $dbConfig->fechaBd();
            }

        /*** Método que mostra os dados do processo selecionado ***/
            function mostraAssunto($cod1, $cod2)
            {
                $dbConfig = new databaseLegado;
                $dbConfig->abreBd();
                $select =   "select cod_assunto, cod_classificacao, nom_assunto
                                from sw_assunto
                                where cod_assunto = '$cod1'
                                and cod_classificacao = '$cod2'";
                //echo $select."<br>";
                $dbConfig->abreSelecao($select);
                $this->codClassificacao = $dbConfig->pegaCampo("cod_classificacao");
                $this->nome = $dbConfig->pegaCampo("nom_assunto");
                $dbConfig->limpaSelecao();
                $selectDoc =    "select cod_documento
                                    from sw_documento_assunto
                                    where cod_assunto = '$cod1'
                                    and cod_classificacao = '$cod2'";
                //echo $selectDoc;
                $dbConfig->abreSelecao($selectDoc);
                $this->documento = array();
                while (!$dbConfig->eof()) {
                    $this->documento[$dbConfig->pegaCampo("cod_documento")] = "s";
                    $dbConfig->vaiProximo();
                }
                $dbConfig->limpaSelecao();
                $dbConfig->fechaBd();
            }

        /*** Método que altera tipo de processo ***/
            function alteraAssunto($cod1, $cod2)
            {
                $dbConfig = new databaseLegado;
                $dbConfig->abreBd();
                $update =   "update sw_assunto set
                                nom_assunto = '$this->nome',
                                confidencial = '$this->confidencial'
                                where cod_assunto = '$cod1'
                                and cod_classificacao = '$cod2'"; //altera os dados na tabela
                //echo $update."<br>";
                $result = $dbConfig->executaSql($update);
                if ($result) {
                    return true;
                    $dbConfig->fechaBd();
                } else {
                    return false;
                    $dbConfig->fechaBd();
                }
            }

        /*** Método que exclui tipo de processo ***/
            function excluiAssunto($cod1, $cod2)
            {
                $dbConfig = new databaseLegado;
                $dbConfig->abreBd();
                $delete =   "delete from sw_documento_assunto";
                $delete .= " where cod_assunto = $cod1";
                $delete .= " and cod_classificacao = $cod2 ;";
                $delete .= " delete from sw_assunto_atributo ";
                $delete .= " where cod_assunto = $cod1 and ";
                $delete .= " cod_classificacao = $cod2 ;";
                $delete .= " delete from sw_assunto";
                $delete .= " where cod_assunto = $cod1";
                $delete .= " and cod_classificacao = $cod2";
                //echo $delete;
                $result = $dbConfig->executaSql($delete);
                $dbConfig->fechaBd();
                if ($result) {
                   return true;
                } else {
                   return false;
                }
            }

        /*** Método que seta variaveis ***/
            function setaVariaveisRel($type, $order)
            {
                $this->tipo = $type;
                $this->ordem = $order;
            }

        /*** Método que gera relatório de tipo de processo ***/
            function geraRelatorio($ordem)
            {
                $dbConfig = new databaseLegado;
                $dbConfig->abreBd();
                if ($this->tipo == 1) {
                    $select =   "select assunto.cod_classificacao || '.' || cod_assunto as codigo, nom_assunto, class.nom_classificacao
                                    from sw_assunto as assunto, sw_classificacao as class
                                    where assunto.cod_classificacao = class.cod_classificacao order by $ordem";
                    //echo $select."<br>";
                    $this->sSQLListaTipoRelatorio = $select;
                    $dbConfig->abreSelecao($select);
                    while (!$dbConfig->eof()) {
                        $lista[] = $dbConfig->pegaCampo("codigo")."/".
                        $dbConfig->pegaCampo("nom_assunto")."/".$dbConfig->pegaCampo("nom_classificacao");
                        $dbConfig->vaiProximo();
                    }
                    $dbConfig->limpaSelecao();

                    return $lista;
                    $dbConfig->fechaBd();
                }
                if ($this->tipo == 2) {
                    $select =   "select assunto.cod_classificacao || '.' || cod_assunto as codigo, c.nom_classificacao, assunto.nom_assunto,
                                assunto.cod_classificacao, cod_assunto
                                    from sw_assunto as assunto, sw_classificacao as c
                                    where assunto.cod_classificacao = c.cod_classificacao order by $ordem";
                    //echo $select."<br>";
                    $this->sSQLListaTipoRelatorio = "select assunto.cod_classificacao || '.' || cod_assunto as codigo, c.nom_classificacao,
                                    assunto.nom_assunto, assunto.cod_classificacao, cod_assunto
                                    from sw_assunto as assunto, sw_classificacao as c
                                    where assunto.cod_classificacao = c.cod_classificacao order by nom_assunto;";
                    $dbConfig->abreSelecao($select);
                    while (!$dbConfig->eof()) {
                        $codclass = $dbConfig->pegaCampo("cod_classificacao");
                        $codass = $dbConfig->pegaCampo("cod_assunto");
                        $codigo = $dbConfig->pegaCampo("codigo");
                        $lista[$codigo]["ass"] = $dbConfig->pegaCampo("codigo")."/".
                        $dbConfig->pegaCampo("nom_classificacao")."/".$dbConfig->pegaCampo("nom_assunto");
                        $dbConfig2 = new databaseLegado;
                        $dbConfig2->abreBd();
                        $select2 = "select nom_documento
                                        from sw_documento as doc, sw_documento_assunto as docass
                                        where docass.cod_documento = doc.cod_documento
                                        and docass.cod_assunto = $codass
                                        and docass.cod_classificacao = $codclass";
                        //echo $select2."<br>";
                        $this->sSQLListaTipoRelatorio .= "select nom_documento
                                        from sw_documento as doc, sw_documento_assunto as docass
                                        where docass.cod_documento = doc.cod_documento
                                        and docass.cod_assunto = &cod_assunto
                                        and docass.cod_classificacao = &cod_classificacao;";
                        $dbConfig2->abreSelecao($select2);
                        while (!$dbConfig2->eof()) {
                            $lista[$codigo]["doc"][] = $dbConfig2->pegaCampo("nom_documento");
                            $dbConfig2->vaiProximo();
                        }
                        $dbConfig2->limpaSelecao();
                        $select3 =   "select cod_assunto, descricao, setor.nom_setor, ordem
                                    from sw_andamento_padrao as anda, administracao.setor as setor, administracao.departamento as depart,
                                    administracao.unidade as unidade, administracao.orgao as orgao
                                    where
                                    anda.cod_setor = setor.cod_setor
                                    and anda.cod_departamento = depart.cod_departamento
                                    and anda.cod_unidade = unidade.cod_unidade
                                    and anda.cod_orgao = orgao.cod_orgao
                                    and setor.cod_departamento = depart.cod_departamento
                                    and depart.cod_unidade = unidade.cod_unidade
                                    and unidade.cod_orgao = orgao.cod_orgao
                                    and setor.cod_unidade = depart.cod_unidade
                                    and setor.cod_orgao = depart.cod_orgao
                                    and depart.cod_orgao = unidade.cod_orgao
                                    and anda.cod_assunto = $codass
                                    and anda.cod_classificacao = $codclass";
                        //echo $select3."<br>";
                        $this->sSQLListaTipoRelatorio .= "select anda.cod_assunto, anda.descricao, setor.nom_setor, ordem
                                    from sw_andamento_padrao as anda, administracao.setor as setor, administracao.departamento as depart,
                                    administracao.unidade as unidade, administracao.orgao as orgao
                                    where
                                    anda.cod_setor = setor.cod_setor
                                    and anda.cod_departamento = depart.cod_departamento
                                    and anda.cod_unidade = unidade.cod_unidade
                                    and anda.cod_orgao = orgao.cod_orgao
                                    and setor.cod_departamento = depart.cod_departamento
                                    and depart.cod_unidade = unidade.cod_unidade
                                    and unidade.cod_orgao = orgao.cod_orgao
                                    and setor.cod_unidade = depart.cod_unidade
                                    and setor.cod_orgao = depart.cod_orgao
                                    and depart.cod_orgao = unidade.cod_orgao
                                    and anda.cod_assunto = &cod_assunto
                                    and anda.cod_classificacao = &cod_classificacao;";
                        $dbConfig2->abreSelecao($select3);
                        while (!$dbConfig2->eof()) {
                        $lista[$codigo]["anda"][] = "<b>Setor: </b>".$dbConfig2->pegaCampo("nom_setor")."<br><b>Ordem: </b>".
                        $dbConfig2->pegaCampo("ordem")."<br> <b>Descrição:</b> ".$dbConfig2->pegaCampo("descricao");
                            $dbConfig2->vaiProximo();
                        }
                        $dbConfig->vaiProximo();
                    }
                    $dbConfig->limpaSelecao();
                    $dbConfig2->limpaSelecao();

                    return $lista;
                    $dbConfig->fechaBd();
                    $dbConfig2->fechaBd();
                }
            }

        /*** Método que busca as os atributos de um assunto ***/
        /*** Retorna um array associativo com os atributos  ***/
        /*** caso tenha algum assunto relacionado ao atributo ***/
        /*** retorna o o cod do assunto e sua classificacao ***/
            function listaAtributos($codAssunto = '', $codClassificacao = '')
            {
               $dbConfig = new databaseLegado;
               $dbConfig->abreBd();
               $select  = " SELECT  ";
               $select .= "    AP.COD_ATRIBUTO, ";
               $select .= "    AP.NOM_ATRIBUTO, ";
               $select .= "    AP.TIPO, ";
               $select .= "    AP.VALOR_PADRAO, ";
               if($codClassificacao )
                    $select .= "    AT.COD_CLASSIFICACAO, ";

                if( $codAssunto)
                    $select .= "    AT.COD_ASSUNTO, ";
               $select = substr($select, 0, strlen($select) - 2 );
               $select .= " FROM ";
               $select .= "    SW_ATRIBUTO_PROTOCOLO AS AP ";
               $select .= "    LEFT JOIN ";
               $select .= "    ( ";
               $select .= "    SELECT ";
               $select .= "       COD_ATRIBUTO, ";
               $select .= "       COD_ASSUNTO, ";
               $select .= "       COD_CLASSIFICACAO ";
               $select .= "    FROM ";
               $select .= "       SW_ASSUNTO_ATRIBUTO ";
               if ($codAssunto or $codClassificacao) {
                  $selectW = "    WHERE ";
               }
               if ($codAssunto) {
                  $selectW .= "       COD_ASSUNTO = ".$codAssunto." AND";
               }
               if ($codClassificacao) {
                  $selectW .= "       COD_CLASSIFICACAO = ".$codClassificacao." AND";
               }
               $select .= substr($selectW, 0, strlen($selectW) - 3);
               $select .= "    ) AS AT ";
               $select .= " ON AT.COD_ATRIBUTO = AP.COD_ATRIBUTO ";
               //if (!$codAssunto  and !$codClassificacao) {
                   $select .= " GROUP BY ";
                   $select .= "    AP.COD_ATRIBUTO, ";
                   $select .= "    AP.NOM_ATRIBUTO, ";
                   $select .= "    AP.TIPO, ";
                   $select .= "    AP.VALOR_PADRAO, ";
               //}

               if ($codAssunto) {
                  $select .= "    AT.COD_ASSUNTO, ";
               }
               if ($codClassificacao) {
                  $select .= "    AT.COD_CLASSIFICACAO, ";
               }
               $select = substr($select,0,strlen($select) - 2);

               $select .= " ORDER BY ";
               $select .= "    AP.NOM_ATRIBUTO ";

               $dbConfig->abreSelecao($select);
               $registros = array();
               while (!$dbConfig->eof()) {
                  $codAtributo = $dbConfig->pegaCampo("COD_ATRIBUTO");
                  $nomAtributo = $dbConfig->pegaCampo("NOM_ATRIBUTO");
                  $tipo = $dbConfig->pegaCampo("TIPO");
                  $valorPadrao = $dbConfig->pegaCampo("VALOR_PADRAO");

                  if ($codAssunto or $codClassificacao) {
                     if ($codAssunto) {
                         $codAss = $dbConfig->pegaCampo("COD_ASSUNTO");
                     } else {
                         $codAss ="";
                     }
                     $codClass = $dbConfig->pegaCampo("COD_CLASSIFICACAO");
                  } else {
                     $codAss = "";
                     $codClass = "";
                  }
                  $regTemp = array( "codAtributo" => $codAtributo,
                                    "nomAtributo" => $nomAtributo,
                                    "tipo" => $tipo,
                                    "valorPadrao" => $valorPadrao,
                                    "codAssunto" => $codAss,
                                    "codClassificacao" => $codClass);
                  $registros[] = $regTemp;
                  $dbConfig->vaiProximo();
               }
               $dbConfig->limpaSelecao();
               $dbConfig->fechaBd();

               return $registros;
            }

            function incluiAtributos($codAssunto, $codClassificacao, $atributos)
            {
               $dbConfig = new databaseLegado;
               $dbConfig->abreBd();
               if ( is_array($atributos) ) {
                  $insert = "";
                  foreach ($atributos as $chave => $valor) {
                     $insert .= " insert into sw_assunto_atributo ";
                     $insert .= " (cod_atributo, cod_assunto, cod_classificacao) values ";
                     $insert .= " ('".$valor."', '".$codAssunto."','".$codClassificacao."');";
                  }
                  if ($dbConfig->executaSql($insert)) {
                     $dbConfig->fechaBd();
                     //echo $insert;
                     return TRUE;
                  } else {
                     $dbConfig->fechaBd();

                     return FALSE;
                  }
               } else {
                  $dbConfig->fechaBd();

                  return TRUE;
               }
            }

            function excluiAtributos($codAssunto, $codClassificacao)
            {
               $dbConfig = new databaseLegado;
               $dbConfig->abreBd();
               $delete  = " delete from sw_assunto_atributo ";
               $delete .= " where cod_assunto = '$codAssunto' and ";
               $delete .= " cod_classificacao = '$codClassificacao' ";
               if ($dbConfig->executaSql($delete)) {
                  $dbConfig->fechaBd();

                  return TRUE;
               } else {
                  $dbConfig->fechaBd();

                  return FALSE;
               }
            }

        /*** Método que lista as classficacoes relacionadas a assuntos ***/
        /*** Retorna um array associativo com os dados do assunto e sua classificacao **/
            function listaClassificacaoAssunto($condicao = "", $order = "")
            {
                $dbConfig = new databaseLegado;
                $dbConfig->abreBd();
                $select  = " SELECT ";
                $select .= "   ass.cod_assunto, ";
                $select .= "   ass.cod_classificacao, ";
                $select .= "   ass.nom_assunto, ";
                $select .= "   ass.confidencial, ";
                $select .= "   cla.nom_classificacao ";
                $select .= " FROM ";
                $select .= "   sw_classificacao AS cla, ";
                $select .= "   sw_assunto as ass ";
                $select .= " WHERE ";
                $select .= "   cla.cod_classificacao = ass.cod_classificacao ";
                if ($condicao) {
                   $select .= $condicao;
                }
                if ($order) {
                   $select .= " ORDER BY ".$order;
                }
                $dbConfig->abreSelecao($select);
                $registros = array();
                while (!$dbConfig->eof()) {
                  $codAssunto = $dbConfig->pegaCampo("cod_assunto");
                  $codClassificacao = $dbConfig->pegaCampo("cod_classificacao");
                  $nomAssunto = $dbConfig->pegaCampo("nom_assunto");
                  $confidencial = $dbConfig->pegaCampo("confidencial");
                  $nomClassificacao = $dbConfig->pegaCampo("nom_classificacao");
                  $regTemp = array( "codAssunto" => $codAssunto,
                                    "codClassificacao" => $codClassificacao,
                                    "nomAssunto" => $nomAssunto,
                                    "confidencial" => $confidencial,
                                    "nomClassificacao" => $nomClassificacao);
                  $registros[] = $regTemp;
                  $dbConfig->vaiProximo();
                }
                $dbConfig->limpaSelecao();
                $dbConfig->fechaBd();

                return $registros;
            }

    }
