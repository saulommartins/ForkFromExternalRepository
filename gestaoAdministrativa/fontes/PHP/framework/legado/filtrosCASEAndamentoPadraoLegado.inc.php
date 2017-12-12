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
*
* Data de Criação: 27/10/2005

* @author Desenvolvedor: Cassiano de Vasconcellos Ferreira
* @author Documentor: Cassiano de Vasconcellos Ferreira

* @package framework
* @subpackage componentes

Casos de uso: uc-01.01.00
*/

$mascaraAssunto = pegaConfiguracao('mascara_assunto',5);
$mascaraSetor   = pegaConfiguracao('mascara_setor',2);

$variavel = $_REQUEST["variavel"];
$valor = $_REQUEST["valor"];

switch ($variavel) {
    case 'codClassifAssunto':
        $codClassifAssuntof = validaMascaraDinamica($mascaraAssunto,$valor);
        $js .= "f.codClassifAssunto.value = \"".$codClassifAssuntof[1]."\";\n";
        $variaveis         = preg_split( "/[^a-zA-Z0-9]/", $_REQUEST["codClassifAssunto"] );
        $codClassificacaof = $variaveis[0];
        $codAssuntof       = $variaveis[1];
        $js .=  "var iContClass = 0;\n";
        $js .=  "var iTamClass = f.codClassificacao.options.length - 1;\n";
        $js .=  "while (iTamClass >= iContClass) {\n";
        $js .=  "    if ( f.codClassificacao.options[iContClass].value == ".(integer) $codClassificacaof.") {\n";
        $js .=  "        f.codClassificacao.options[iContClass].selected = true;\n";
        $js .=  "        break;\n";
        $js .=  "    }\n";
        $js .=  "    iContClass++;\n";
        $js .=  "}\n";
        $js .=  "if (iContClass > iTamClass) {\n";
        $js .=  "    f.codClassificacao.options[0].selected = true;\n";
        $js .=  "    limpaSelect(f.codAssunto,1); \n";
        $js .=  "}\n";
        //Faz o combo de Assunto
        if ($codClassificacaof == $_REQUEST["codClassificacao"]) {
             $js .=  "var iContAss = 0;\n";
             $js .=  "var iTamAss = f.codAssunto.options.length - 1;\n";
             $js .=  "while (iTamAss >= iContAss) {\n";
             $js .=  "    if ( f.codAssunto.options[iContAss].value == ".(integer) $codAssuntof.") {\n";
             $js .=  "        f.codAssunto.options[iContAss].selected = true;\n";
             //$js .=  "		  f.ok.disabled = false;\n";
             $js .=  "        break;\n";
             $js .=  "    }\n";
             $js .=  "    iContAss++;\n";
             $js .=  "}\n";
             $js .=  "if (iContAss > iTamAss) {\n";
             $js .=  "    f.codAssunto.options[0].selected = true;\n";
             $js .=  "}\n";
        } else {

            $sSQL  = "SELECT cod_assunto, cod_classificacao, nom_assunto FROM sw_assunto ";
            $sSQL .= "WHERE cod_classificacao = '".$codClassificacaof."' ORDER by nom_assunto";
            //echo  $sSQL;
            $dbAss = new dataBaseLegado;
            $dbAss->abreBD();
            $dbAss->abreSelecao($sSQL);
            $dbAss->vaiPrimeiro();
            $contAss = 1;
            $js .= "limpaSelect(f.codAssunto,1); \n";
            while (!$dbAss->eof()) {
                $codAssuntoW  = trim($dbAss->pegaCampo("cod_assunto"));
                $nomAssuntoW  = trim($dbAss->pegaCampo("nom_assunto"));
                $dbAss->vaiProximo();
                if ($codAssuntoW == $codAssuntof) {
                    $selected = ", true";
                } else {
                    $selected = "";
                }
                $js .= "f.codAssunto.options[$contAss] = new Option('".$nomAssuntoW."','".$codAssuntoW."'".$selected."); \n";
                $contAss++;
            }
            $dbAss->limpaSelecao();
            $dbAss->fechaBD();

        }
    break;
    case 'codClassificacao':
        if ($valor == "xxx") {
            $codClassifAssuntof = validaMascaraDinamica($mascaraAssunto,"");
            $default = ", true";
            $js .= "f.codClassifAssunto.value = '".$codClassifAssuntof[1]."';\n";
            $js .= "limpaSelect(f.codAssunto,0); \n";
            $js .= "f.codAssunto.options[0] = new Option('Selecione','xxx'".$default.");\n";
        } else {
            //Faz o combo de Assunto
            $sSQL  = "SELECT cod_assunto, cod_classificacao, nom_assunto FROM sw_assunto ";
            $sSQL .= "WHERE cod_classificacao = ".$valor." ORDER by nom_assunto";
            $codClassifAssuntof = validaMascaraDinamica($mascaraAssunto,$valor);
            $js .= "f.codClassifAssunto.value = '".$codClassifAssuntof[1]."';\n";
            $dbAss = new dataBaseLegado;
            $dbAss->abreBD();
            $dbAss->abreSelecao($sSQL);
            $dbAss->vaiPrimeiro();
            $contAss = 1;
            $js .= "limpaSelect(f.codAssunto,1); \n";
            while (!$dbAss->eof()) {
                $codAssuntof  = trim($dbAss->pegaCampo("cod_assunto"));
                $nomAssuntof  = trim($dbAss->pegaCampo("nom_assunto"));
                $dbAss->vaiProximo();
                $js .= "f.codAssunto.options[$contAss] = new Option('".$nomAssuntof."','".$codAssuntof."'); \n";
                $contAss++;
            }
            if ($contAss == 1) {
                $js .= "limpaSelect(f.codAssunto,0); \n";
                $js .= "f.codAssunto.options[0] = new Option('Selecione','xxx'".$default.");\n";
            }
            $dbAss->limpaSelecao();
            $dbAss->fechaBD();
        }
    break;
    case 'codAssunto':
        //Faz o combo de Assunto
        $sSQL  = "SELECT cod_assunto, cod_classificacao, nom_assunto FROM sw_assunto ";
        $sSQL .= " WHERE cod_classificacao = ".$_REQUEST["codClassificacao"]." ORDER by nom_assunto";
        //echo  $sSQL;
        $dbAss = new dataBaseLegado;
        $dbAss->abreBD();
        $dbAss->abreSelecao($sSQL);
        $dbAss->vaiPrimeiro();
        if ($codAssunto == "xxx") {
            $codAssunto = "00";
        }
        $valor = $_REQUEST["codClassificacao"]."-".$_REQUEST["codAssunto"];
        $codClassifAssuntof = validaMascaraDinamica($mascaraAssunto, $valor);
        $js .= "f.codClassifAssunto.value = '".$codClassifAssuntof[1]."';\n";
        $contAss = 1;
        while (!$dbAss->eof()) {
            $codAssuntof  = trim($dbAss->pegaCampo("cod_assunto"));
            $nomAssuntof  = trim($dbAss->pegaCampo("nom_assunto"));
            $dbAss->vaiProximo();
            $js .= "f.codAssunto.options[$contAss] = new Option('".$nomAssuntof."','".$codAssuntof."'); \n";
            if ($codAssuntof == $codAssunto) {
                $js .= "f.codAssunto.options[$contAss].selected = true; \n";
            }
            $contAss++;
        }
        if ($contAss == 1) {
            $js .= "limpaSelect(f.codAssunto,1); \n";
        }
        $dbAss->limpaSelecao();
        $dbAss->fechaBD();
    break;
    case 'codMasSetor':

            $codMasSetorF     = validaMascaraDinamica($mascaraSetor,$valor);
            $js              .= "f.codMasSetor.value = \"".$codMasSetorF[1]."\";\n";
            $arVariaveis      = preg_split( "/[^a-zA-Z0-9]/", $valor );
            $codOrgaoF        = $arVariaveis[0];
            $codUnidadeF      = $arVariaveis[1];
            $codDepartamentoF = $arVariaveis[2];
            $codSetorF        = $arVariaveis[3];
            $anoExercicio     = $arVariaveis[4];

            //VERIFICA O COMBO ORGAO
            $js .=  "var iContOrgao = 0;\n";
            $js .=  "var iTamOrgao = f.codOrgao.options.length - 1;\n";
            //$js .=  "f.anoExercicio.value = ".$anoExercicio.";";
            $js .=  "while (iTamOrgao >= iContOrgao) {\n";
            $js .=  "    if ( f.codOrgao.options[iContOrgao].value == ".(integer) $codOrgaoF.$anoExercicio." ) {\n";
            $js .=  "        f.codOrgao.options[iContOrgao].selected = true;\n";
            $js .=  "        break;\n";
            $js .=  "    }\n";
            $js .=  "    iContOrgao++;\n";
            $js .=  "}\n";
            $js .=  "if (iContOrgao > iTamOrgao) {\n";
            $js .=  "    limpaSelect(f.codUnidade,1); \n";
            $js .=  "    limpaSelect(f.codDepartamento,1); \n";
            $js .=  "    limpaSelect(f.codSetor,1); \n";
            $js .=  "}\n";
            $sSQL = "SELECT cod_orgao, nom_orgao, ano_exercicio from administracao.orgao where cod_orgao = '".$codOrgaoF."'
            and ano_exercicio = '".$anoExercicio."' ";
            $dbOrg = new dataBaseLegado;
            $dbOrg->abreBD();
            $dbOrg->abreSelecao($sSQL);
            $dbOrg->vaiPrimeiro();
            $boRegistrosOrg = $dbOrg->eof();
            $dbOrg->limpaSelecao();
            $dbOrg->fechaBD();
            if (!$boRegistrosOrg) {
                $js   .=  "        f.nomOrgao.value = \"".$dbOrg->pegaCampo("nom_orgao")."\";\n";
                $js   .=  "        f.anoExercicio.value = \"".$dbOrg->pegaCampo("ano_exercicio")."\";\n";
                $sSQL  = "SELECT cod_orgao, cod_unidade, nom_unidade FROM administracao.unidade ";
                $sSQL .= "WHERE cod_orgao = '".$codOrgaoF."' and ano_exercicio = '".$anoExercicio."'
                ORDER by nom_unidade";

                $dbUni = new dataBaseLegado;
                $dbUni->abreBD();
                $dbUni->abreSelecao($sSQL);
                $dbUni->vaiPrimeiro();
                $boRegistroUni = $dbUni->eof();
                $contUni = 1;
                if (!$boRegistroUni) {

                    $js .= "limpaSelect(f.codUnidade,0); \n";
                    $js .= "f.codUnidade.options[0] = new Option('Selecione','xxx');\n";
                    //MONTA O COMBO UNIDADE
                    while (!$dbUni->eof()) {
                        $codUnidadeC  = trim($dbUni->pegaCampo("cod_unidade"));
                        $nomUnidadeC  = trim($dbUni->pegaCampo("nom_unidade"));
                        //$dbUni->vaiProximo();
                        if ($codUnidadeC == $codUnidadeF) {
                            $selected = ", true";
                            $js .=  "        f.nomUnidade.value = \"".$nomUnidadeC."\";\n";
                        } else {
                            $selected = "";
                        }
                        $js .= "f.codUnidade.options[$contUni] = new Option('".$nomUnidadeC."','".$codUnidadeC."-".$codOrgaoF."'".$selected."); \n";
                        $contUni++;
                        $dbUni->vaiProximo();
                    }
                    $js .=  "f.codUnidade.focus();\n";
                    $dbUni->limpaSelecao();
                    $dbUni->fechaBD();
                    if ($codUnidadeF) {
                        $sSQL  = "SELECT cod_unidade, cod_departamento, nom_departamento FROM administracao.departamento ";
                        $sSQL .= "WHERE cod_unidade = '".$codUnidadeF."' and cod_orgao='".$codOrgaoF."'
                        and ano_exercicio = '".$anoExercicio."' ORDER by nom_departamento";

                        $dbDep = new dataBaseLegado;
                        $dbDep->abreBD();
                        $dbDep->abreSelecao($sSQL);
                        $dbDep->vaiPrimeiro();
                        $boRegistroDep = $dbDep->eof();
                        $contDep = 1;
                        //MONTA O CAMBO DEPARTAMENTO
                        $js .= "limpaSelect(f.codDepartamento,0); \n";
                        $js .= "f.codDepartamento.options[0] = new Option('Selecione','xxx');\n";
                        while (!$dbDep->eof()) {
                            $codDepartamentoC  = trim($dbDep->pegaCampo("cod_departamento"));
                            $nomDepartamentoC  = trim($dbDep->pegaCampo("nom_departamento"));
                            if ($codDepartamentoC == $codDepartamentoF) {
                                $selected = ", true";
                                $js .=  "        f.nomDepartamento.value = \"".$nomDepartamentoC."\";\n";
                            } else {
                                $selected = "";
                            }
                            $js .= "f.codDepartamento.options[$contDep] = new Option('".$nomDepartamentoC."','".$codDepartamentoC."-".$codUnidadeF."-".$codOrgaoF."'".$selected."); \n";
                            $contDep++;
                            $dbDep->vaiProximo();
                        }
                        $js .=  "f.codDepartamento.focus();\n";
                        $dbDep->limpaSelecao();
                        $dbDep->fechaBD();
                        if ($codSetorF) {
                            $sSQL  = " SELECT cod_setor, nom_setor, ano_exercicio FROM administracao.setor ";
                            $sSQL .= " WHERE cod_departamento = '".$codDepartamentoF."' and ";
                            $sSQL .= " cod_unidade = '".$codUnidadeF."' and ";
                            $sSQL .= " cod_orgao='".$codOrgaoF."'
                            and ano_exercicio = '".$anoExercicio."' ORDER by nom_setor";
                            //echo  $sSQL;
                            $dbSet = new dataBaseLegado;
                            $dbSet->abreBD();
                            $dbSet->abreSelecao($sSQL);
                            $dbSet->vaiPrimeiro();
                            $contSet = 1;
                            //MONTA O CAMBO DEPARTAMENTO
                            $js .= "limpaSelect(f.codSetor,0); \n";
                            $js .= "f.codSetor.options[0] = new Option('Selecione','xxx');\n";
                            while (!$dbSet->eof()) {
                                $codSetorC      = trim($dbSet->pegaCampo("cod_setor"));
                                $nomSetorC      = trim($dbSet->pegaCampo("nom_setor"));
                                $anoExercicioC  = trim($dbSet->pegaCampo("ano_exercicio"));
                                if ($codSetorC == $codSetorF) {
                                    $selected = ", true";
                                    $js .=  "        f.nomSetor.value = \"".$nomSetorC."\";\n";
                                    $js .=  "        f.anoExercicioSetor.value = \"".$anoExercicioC."\";\n";
                                    //$js .= "   f.ok.disabled = false;\n";
                                } else {
                                    $selected = "";
                                }
                                $js .= "f.codSetor.options[$contSet] = new Option('".$nomSetorC."','".$codSetorC."-".$codDepartamentoF."-".$codUnidadeF."-".$codOrgaoF."'".$selected."); \n";
                                $contSet++;
                                $dbSet->vaiProximo();
                            }
                            $js .=  "f.codSetor.focus();\n";
                            $dbSet->limpaSelecao();
                            $dbSet->fechaBD();
                        }
                    } else {
                        $js .=  "    limpaSelect(f.codSetor,1); \n";
                    }
                } else {
                    $js .=  "    limpaSelect(f.codUnidade,1); \n";
                    $js .=  "    limpaSelect(f.codDepartamento,1); \n";
                    $js .=  "    limpaSelect(f.codSetor,1); \n";
                }

            } else {
                $js .=  "    limpaSelect(f.codUnidade,1); \n";
                $js .=  "    limpaSelect(f.codDepartamento,1); \n";
                $js .=  "    limpaSelect(f.codSetor,1); \n";
            }
    break;
    case 'codOrgao':

        if ($codOrgao != "xxx") {
            $codOrgaom = $valor;
            //Faz o combo de Órgãos
/*            if ($anoExercicio != "") {
                $anoExercicio = " and ano_exercicio = ".$anoExercicio." ";
            }*/
            $anoExercicio="";
            if ($anoOrgao != "") {
                $anoOrgaoDesc = " and ano_exercicio = '".$anoOrgao."' ";
            }

            $sSQL = "SELECT cod_unidade, nom_unidade FROM administracao.unidade WHERE cod_orgao = ".$codOrgaom."
            ".$anoExercicio." ".$anoOrgaoDesc." ORDER by nom_unidade";

            //echo  $sSQL;
            $dbEmp = new dataBaseLegado;
            $dbEmp->abreBD();
            $dbEmp->abreSelecao($sSQL);
            $dbEmp->vaiPrimeiro();
            $cont = 1;
            $js = "";
            $codMasSetor = validaMascaraDinamica($mascaraSetor,$valor);
            $js .= "f.codMasSetor.value = \"".$codMasSetor[1]."\";\n";
            $js .= "limpaSelect(f.codUnidade,0); \n";
            $js .= "f.codUnidade.options[0] = new Option('Selecione','xxx'".$default."); \n";
            while (!$dbEmp->eof()) {
                $codUnidadef  = trim($dbEmp->pegaCampo("cod_unidade"));
                $nomUnidadef  = trim($dbEmp->pegaCampo("nom_unidade"));
                $chaveU = $codUnidadef."-".$codOrgaom."-".$anoOrgao;
                $dbEmp->vaiProximo();
                $js .= "f.codUnidade.options[$cont] = new Option('".$nomUnidadef."','".$chaveU."'".$default."); \n";
                $cont++;
            }
            $js .= "f.codUnidade.options[0].selected = true;\n";
            $js .= "limpaSelect(f.codDepartamento,0); \n";
            $js .= "f.codDepartamento.options[0] = new Option('Selecione','xxx'".$default."); \n";
            $js .= "limpaSelect(f.codSetor,0); \n";
            $js .= "f.codSetor.options[0] = new Option('Selecione','xxx'".$default."); \n";
            $dbEmp->limpaSelecao();
            $dbEmp->fechaBD();
        } else {
            $js = "limpaSelect(f.codUnidade,0); \n";
            $js .= "f.codUnidade.options[0] = new Option('Selecione','xxx'".$default."); \n";
            $js .= "limpaSelect(f.codDepartamento,0); \n";
            $js .= "f.codDepartamento.options[0] = new Option('Selecione','xxx'".$default."); \n";
            $js .= "limpaSelect(f.codSetor,0); \n";
            $js .= "f.codSetor.options[0] = new Option('Selecione','xxx'".$default."); \n";
            $codMasSetor = validaMascaraDinamica($mascaraSetor,"0");
            $js .= "f.codMasSetor.value = \"".$codMasSetor[1]."\";\n";
        }
    break;
    case 'codUnidade':
    $codUnidade = $_REQUEST["codUnidade"];
    $anoOrgao   = $_REQUEST["anoOrgao"];
    if ($codUnidade != "xxx") {
        $variaveis = explode("-",$codUnidade);
            $codUnidaded = $variaveis[0];
            $codOrgaod = $variaveis[1];
        $anoOrgao = $variaveis[2];

        if ($anoOrgao != "") {
                $anoOrgaoDesc = " and ano_exercicio = '".$anoOrgao."' ";
            }

            //Faz o combo de Órgãos
            $sSQL  = "SELECT cod_departamento, nom_departamento FROM ";
            $sSQL .= "administracao.departamento WHERE cod_unidade = ".$codUnidaded;
            $sSQL .= " AND cod_orgao = ".$codOrgaod;
//            $sSQL .= $anoExercicio;
            $sSQL .= $anoOrgaoDesc;
            $sSQL .= " ORDER by nom_departamento";

        //echo $sSQL . " SQL";

        $dbEmp = new dataBaseLegado;
            $dbEmp->abreBD();
            $dbEmp->abreSelecao($sSQL);
            $dbEmp->vaiPrimeiro();
            $cont = 1;
            $js = "";
            $codMasSetor = validaMascaraDinamica($mascaraSetor, $codOrgaod."-".$codUnidaded);
            $js .= "f.codMasSetor.value = \"".$codMasSetor[1]."\";\n";
            $js .= "limpaSelect(f.codDepartamento,0); \n";
            $js .= "f.codDepartamento.options[0] = new Option('Selecione','xxx'".$default."); \n";
            while (!$dbEmp->eof()) {
                $codDepartamentof  = trim($dbEmp->pegaCampo("cod_departamento"));
                $nomDepartamentof  = trim($dbEmp->pegaCampo("nom_departamento"));
                $chaveD = $codDepartamentof."-".$codUnidaded."-".$codOrgaod."-".$codAnod."-".$anoOrgao;
                $dbEmp->vaiProximo();
                $js .= "f.codDepartamento.options[$cont] = new Option('".$nomDepartamentof."','".$chaveD."'".$default."); ";
                $cont++;
            }
            $js .= "f.codDepartamento.options[0].selected = true; \n";
            $js .= "limpaSelect(f.codSetor,0); \n";
            $js .= "f.codSetor.options[0] = new Option('Selecione','xxx'".$default."); \n";
            $dbEmp->limpaSelecao();
            $dbEmp->fechaBD();
        } else {
            $js = "limpaSelect(f.codDepartamento,0); \n";
            $js .= "f.codDepartamento.options[0] = new Option('Selecione','xxx'".$default."); \n";
            $js .= "limpaSelect(f.codSetor,0); \n";
            $js .= "f.codSetor.options[0] = new Option('Selecione','xxx'".$default."); \n";
            $variaveis = explode("-","0-".$codOrgao);
            $codUnidaded = $variaveis[0];
            $codOrgaod = $variaveis[1];
            $codMasSetor = validaMascaraDinamica($mascaraSetor, $codOrgaod."-".$codUnidaded);
            $js .= "f.codMasSetor.value = \"".$codMasSetor[1]."\";\n";
        }
    break;
    case 'codDepartamento':
    $codDepartamento = $_REQUEST["codDepartamento"];
    $anoOrgao        = $_REQUEST["anoOrgao"];

        if ($codDepartamento != "xxx") {
            $variaveis = explode("-",$codDepartamento);
            $codDepartamentoS = $variaveis[0];
            $codUnidadeS = $variaveis[1];
            $codOrgaoS = $variaveis[2];

            $anoOrgao = $variaveis[4];

            if ($anoOrgao != "") {
            $anoOrgaoDesc = " and ano_exercicio = '".$anoOrgao."' ";
        }

            //Faz o combo de Órgãos
            $sSQL  = "SELECT cod_setor, nom_setor, ano_exercicio FROM administracao.setor WHERE ";
            $sSQL .= "cod_departamento = ".$codDepartamentoS." AND ";
            $sSQL .= "cod_unidade = ".$codUnidadeS." AND cod_orgao = ".$codOrgaoS." ";
//            $sSQL .= $anoExercicio;
            $sSQL .= $anoOrgaoDesc;
            $sSQL .= " ORDER by nom_setor";
            $dbEmp = new dataBaseLegado;
            $dbEmp->abreBD();
            $dbEmp->abreSelecao($sSQL);
            $dbEmp->vaiPrimeiro();
            $cont = 1;
            $js = "";
            $codMasSetor = validaMascaraDinamica($mascaraSetor,$codOrgaoS."-".$codUnidadeS."-".$codDepartamentoS);
            $js .= "f.codMasSetor.value = \"".$codMasSetor[1]."\";\n";
            $js .= "limpaSelect(f.codSetor,0); \n";
            $js .= "f.codSetor.options[0] = new Option('Selecione','xxx'".$default."); \n";
            while (!$dbEmp->eof()) {
                $codSetorf  = trim($dbEmp->pegaCampo("cod_setor"));
                $nomSetorf  = trim($dbEmp->pegaCampo("nom_setor"));
                $anoExercicioSetor = $dbEmp->pegaCampo("ano_exercicio");
                $chaveS = $codSetorf."-".$codDepartamentoS."-".$codUnidadeS."-".$codOrgaoS."-".$anoExercicioSetor;
                $dbEmp->vaiProximo();
                $js .= "f.codSetor.options[$cont] = new Option('".$nomSetorf  ."','".$chaveS."'".$default."); \n";
                $cont++;
            }
            $js .= "f.codSetor.options[0].selected = true \n";
            $dbEmp->limpaSelecao();
            $dbEmp->fechaBD();
        } else {
            $js = "limpaSelect(f.codSetor,0); \n";
            $js .= "f.codSetor.options[0] = new Option('Selecione','xxx'".$default."); \n";
            $variaveis = explode("-","0-".$codUnidade);
            $codDepartamentoS = $variaveis[0];
            $codUnidadeS = $variaveis[1];
            $codOrgaoS = $variaveis[2];
            $codMasSetor = validaMascaraDinamica($mascaraSetor,$codOrgaoS."-".$codUnidadeS."-".$codDepartamentoS);
            $js .= "f.codMasSetor.value = \"".$codMasSetor[1]."\";\n";
        }
    break;
    case 'codSetor':
    $codSetor        = $_REQUEST["codSetor"];
    $codDepartamento = $_REQUEST["codDepartamento"];
        if ($codSetor == "xxx") {
            $variaveis = explode("-","0-".$codDepartamento);
        } else {
            $variaveis = explode("-",$codSetor);
        }

           $codSetorS = $variaveis[0];
        $codDepartamentoS = $variaveis[1];
        $codUnidadeS = $variaveis[2];
        $codOrgaoS = $variaveis[3];
    //marcia
    $anoExercicioSetor = $variaveis[4];
    //$codMasSetor = validaMascaraDinamica($mascaraSetor,$codOrgaoS."-".$codUnidadeS."-".$codDepartamentoS."-".$codSetorS."/".$anoExercicio);

    $sSQL  = " SELECT administracao.setor.cod_setor, administracao.setor.nom_setor, administracao.setor.ano_exercicio FROM administracao.setor ";
    $sSQL .= " LEFT OUTER JOIN administracao.orgao on administracao.orgao.cod_orgao = '".$codOrgaoS."' ";
    $sSQL .= " WHERE cod_setor = '".$codSetorS."' and ";
        $sSQL .= " cod_departamento = '".$codDepartamentoS."' and ";
        $sSQL .= " cod_unidade = '".$codUnidadeS."' and ";
        $sSQL .= " administracao.setor.cod_orgao='".$codOrgaoS."'
                  and administracao.setor.ano_exercicio = administracao.orgao.ano_exercicio ORDER by nom_setor";

        //$sSQL  = " SELECT cod_setor, nom_setor, ano_exercicio FROM administracao.setor ";
        //$sSQL .= " WHERE cod_setor = '".$codSetorS."' and ";
        //$sSQL .= " cod_departamento = '".$codDepartamentoS."' and ";
        //$sSQL .= " cod_unidade = '".$codUnidadeS."' and ";
        //$sSQL .= " cod_orgao='".$codOrgaoS."'
        //          and ano_exercicio = ".$anoExercicio." ORDER by nom_setor";
        //marcia

        $dbSet = new dataBaseLegado;
        $dbSet->abreBD();
        $dbSet->abreSelecao($sSQL);
        $dbSet->vaiPrimeiro();

        $nomSetor      = trim($dbSet->pegaCampo("nom_setor"));
        $anoExercicio  = trim($dbSet->pegaCampo("ano_exercicio"));

        $codMasSetor = validaMascaraDinamica($mascaraSetor,$codOrgaoS."-".$codUnidadeS."-".$codDepartamentoS."-".$codSetorS."/".$anoExercicioSetor);

        $js .=  "        f.nomSetor.value = \"".$nomSetor."\";\n";
        $js .=  "        f.anoExercicioSetor.value = \"".$anoExercicio."\";\n";

        $js .= "f.codMasSetor.value = \"".$codMasSetor[1]."\";\n";

    break;
    }

executaFrameOculto($js);

?>
