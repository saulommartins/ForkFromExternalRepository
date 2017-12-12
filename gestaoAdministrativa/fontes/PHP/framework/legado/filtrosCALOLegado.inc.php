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

include_once 'dataBaseLegado.class.php';
$mascaraSetor   = pegaConfiguracao('mascara_local',2);

$valor2 = $_REQUEST['valor2'];
$variavel = $_REQUEST['variavel'];
$codOrgao = $_REQUEST['codOrgao'];
$nomOrgao = $_REQUEST['nomOrgao'];
$codUnidade = $_REQUEST['codUnidade'];
$nomUnidade = $_REQUEST['nomUnidade'];
$codDepartamento = $_REQUEST['codDepartamento'];
$nomDepartamento = $_REQUEST['nomDepartamento'];
$codSetor = $_REQUEST['codSetor'];
$nomSetor = $_REQUEST['nomSetor'];
$anoExercicioSetor = $_REQUEST['anoExercicioSetor'];
$codLocal = $_REQUEST['codLocal'];
$nomLocal = $_REQUEST['nomLocal'];
$anoExercicioLocal = $_REQUEST['anoExercicioLocal'];
$tipoRelatorio = $_REQUEST['tipoRelatorio'];

if (isset($valor2)) {
    $valor = $valor2;
}

switch ($variavel) {

    case 'codMasSetor':
            if ($valor != "") {

                $codMasSetorF     = validaMascaraDinamica($mascaraSetor,$valor);
                $js              .= "f.codMasSetor.value = \"".$codMasSetorF[1]."\";\n";
                $arVariaveis      = preg_split( "/[^a-zA-Z0-9]/", $valor );
                $codOrgaoF        = (int) $arVariaveis[0];
                $codUnidadeF      = (int) $arVariaveis[1];
                $codDepartamentoF = (int) $arVariaveis[2];
                $codSetorF        = (int) $arVariaveis[3];
                $codLocalF        = (int) $arVariaveis[4];
                $anoExercicio     = (int) $arVariaveis[5];

                //VERIFICA O COMBO ORGAO
                $js .=  "var iContOrgao = 0;\n";
                $js .=  "var iTamOrgao = f.codOrgao.options.length - 1;\n";
                $js .=  "while (iTamOrgao >= iContOrgao) {\n";
                $js .=  "    if ( f.codOrgao.options[iContOrgao].value == '".(integer) $codOrgaoF."-".$anoExercicio."') {\n";
                $js .=  "        f.codOrgao.options[iContOrgao].selected = true;\n";
                $js .=  "        break;\n";
                $js .=  "    }\n";
                $js .=  "    iContOrgao++;\n";
                $js .=  "}\n";
                $js .=  "if (iContOrgao > iTamOrgao) {\n";
                $js .=  "    limpaSelect(f.codUnidade,1); \n";
                $js .=  "    limpaSelect(f.codDepartamento,1); \n";
                $js .=  "    limpaSelect(f.codSetor,1); \n";
                $js .=  "    limpaSelect(f.codLocal,1); \n";
                $js .=  "}\n";

                if ($anoExercicio != "") {
                    $stExercicio  = $anoExercicio;
                    $anoExercicio = " and ano_exercicio = '".$anoExercicio."' ";
                }

                $sSQL = "SELECT cod_orgao, nom_orgao from administracao.orgao where cod_orgao = '".$codOrgaoF."'
                ".$anoExercicio;

                $dbOrg = new dataBaseLegado;
                $dbOrg->abreBD();
                $dbOrg->abreSelecao($sSQL);
                $dbOrg->vaiPrimeiro();

                $boRegistrosOrg = $dbOrg->eof();
                $dbOrg->limpaSelecao();
                $dbOrg->fechaBD();

                if (!$boRegistrosOrg) {
                    $js   .=  "f.nomOrgao.value = \"".$dbOrg->pegaCampo("nom_orgao")."\";\n";
                    $sSQL  = "SELECT cod_orgao, cod_unidade, nom_unidade FROM administracao.unidade ";
                    $sSQL .= "WHERE cod_orgao = '".$codOrgaoF."'".$anoExercicio." ORDER by nom_unidade";

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

                            if ($codUnidadeC == $codUnidadeF) {
                                $selected = ", true";
                                $js .=  "        f.nomUnidade.value = \"".$nomUnidadeC."\";\n";
                            } else {
                                $selected = "";
                            }
                            $js .= "f.codUnidade.options[$contUni] = new Option('".$nomUnidadeC."','".$codUnidadeC."-".$codOrgaoF."-".$stExercicio."'".$selected."); \n";
                            $contUni++;
                            $dbUni->vaiProximo();
                        }
                        $dbUni->limpaSelecao();
                        $dbUni->fechaBD();

                        if ($codUnidadeF) {
                            $sSQL  = "SELECT cod_unidade, cod_departamento, nom_departamento FROM administracao.departamento ";
                            $sSQL .= "WHERE cod_unidade = '".$codUnidadeF."' and cod_orgao='".$codOrgaoF."'
                            ".$anoExercicio." ORDER by nom_departamento";

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
                                $js .= "f.codDepartamento.options[$contDep] = new Option('".$nomDepartamentoC."','".$codDepartamentoC."-".$codUnidadeF."-".$codOrgaoF."-".$stExercicio."'".$selected."); \n";
                                $contDep++;
                                $dbDep->vaiProximo();
                            }

                            $dbDep->limpaSelecao();
                            $dbDep->fechaBD();

                            if ($codSetorF) {
                                $sSQL  = " SELECT cod_setor, nom_setor, ano_exercicio FROM administracao.setor ";
                                $sSQL .= " WHERE cod_departamento = '".$codDepartamentoF."' and ";
                                $sSQL .= " cod_unidade = '".$codUnidadeF."' and ";
                                $sSQL .= " cod_orgao='".$codOrgaoF."' ".$anoExercicio." ORDER by nom_setor";

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

                                    } else {
                                        $selected = "";
                                    }
                                    $js .= "f.codSetor.options[$contSet] = new Option('".$nomSetorC."','".$codSetorC."-".$codDepartamentoF."-".$codUnidadeF."-".$codOrgaoF."-".$stExercicio."'".$selected."); \n";
                                    $contSet++;
                                    $dbSet->vaiProximo();
                                }
                                $dbSet->limpaSelecao();
                                $dbSet->fechaBD();
                            } else {
                                $js .=  "    limpaSelect(f.codLocal,1); \n";
                            }

                            $sSQL  = " SELECT cod_local, nom_local, ano_exercicio FROM administracao.local ";
                            $sSQL .= " WHERE cod_setor  = '".$codSetorF."'        and ";
                            $sSQL .= " cod_departamento = '".$codDepartamentoF."' and ";
                            $sSQL .= " cod_unidade      = '".$codUnidadeF."'      and ";
                            $sSQL .= " cod_orgao        = '".$codOrgaoF."'
                            ".$anoExercicio." ORDER by nom_local";

                            $dbSet = new dataBaseLegado;
                            $dbSet->abreBD();
                            $dbSet->abreSelecao($sSQL);
                            $dbSet->vaiPrimeiro();
                            $contSet = 1;
                            //MONTA O CAMBO SETOR
                            $js .= "limpaSelect(f.codLocal,0); \n";
                            $js .= "f.codLocal.options[0] = new Option('Selecione','xxx');\n";
                            while (!$dbSet->eof()) {
                                $codLocalC      = trim($dbSet->pegaCampo("cod_local"));
                                $nomLocalC      = trim($dbSet->pegaCampo("nom_local"));
                                $anoExercicioC  = trim($dbSet->pegaCampo("ano_exercicio"));
                                if ($codLocalC == $codLocalF) {
                                    $selected = ", true";
                                    $js .=  "        f.nomLocal.value = \"".$nomLocalC."\";\n";
                                    $js .=  "        f.anoExercicioLocal.value = \"".$anoExercicioC."\";\n";
                                } else {
                                    $selected = "";
                                }
                                $js .= "f.codLocal.options[$contSet] = new Option('".$nomLocalC."','".$codLocalC."-".$codSetorF."-".$codDepartamentoF."-".$codUnidadeF."-".$codOrgaoF."-".$anoExercicioC."'".$selected."); \n";
                                $contSet++;
                                $dbSet->vaiProximo();
                            }
                            $dbSet->limpaSelecao();
                            $dbSet->fechaBD();
                        } else {
                            $js .=  "    limpaSelect(f.codSetor,1); \n";
                            $js .=  "    limpaSelect(f.codLocal,1); \n";
                        }
                    } else {
                        $js .=  "    limpaSelect(f.codUnidade,1); \n";
                        $js .=  "    limpaSelect(f.codDepartamento,1); \n";
                        $js .=  "    limpaSelect(f.codSetor,1); \n";
                        $js .=  "    limpaSelect(f.codLocal,1); \n";
                    }
                } else {
                    $js .=  "    limpaSelect(f.codUnidade,1); \n";
                    $js .=  "    limpaSelect(f.codDepartamento,1); \n";
                    $js .=  "    limpaSelect(f.codSetor,1); \n";
                    $js .=  "    limpaSelect(f.codLocal,1); \n";
                }
            } else {
                $js .=  "    f.codOrgao.value = 'xxx'; \n";
                $js .=  "    limpaSelect(f.codUnidade,1); \n";
                $js .=  "    f.codUnidade.options[0] = new Option('Selecione','xxx');\n";
                $js .=  "    limpaSelect(f.codDepartamento,1); \n";
                $js .=  "    f.codDepartamento.options[0] = new Option('Selecione','xxx');\n";
                $js .=  "    limpaSelect(f.codSetor,1); \n";
                $js .=  "    f.codSetor.options[0] = new Option('Selecione','xxx');\n";
                $js .=  "    limpaSelect(f.codLocal,1); \n";
                $js .=  "    f.codLocal.options[0] = new Option('Selecione','xxx');\n";
            }
    break;

    case 'codOrgao':
        if ($codOrgao != "xxx") {
            $variaveis = explode("-",$codOrgao);
            $codOrgaom = $variaveis[0];
            $anoExercicio = $variaveis[1];
            //Faz o combo de Órgãos
            $sSQL = "SELECT cod_unidade, nom_unidade, ano_exercicio FROM administracao.unidade WHERE cod_orgao = ".$codOrgaom." and ano_exercicio= '".$anoExercicio."' ORDER by nom_unidade";
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

                $chaveU = $codUnidadef."-".$codOrgaom."-".$anoExercicio;
                $dbEmp->vaiProximo();
                $js .= "f.codUnidade.options[$cont] = new Option('".$nomUnidadef."','".$chaveU."'".$default."); \n";
                $cont++;
            }
            $js .= "f.codUnidade.options[0].selected = true;\n";
            $js .= "limpaSelect(f.codDepartamento,0); \n";
            $js .= "f.codDepartamento.options[0] = new Option('Selecione','xxx'".$default."); \n";
            $js .= "limpaSelect(f.codSetor,0); \n";
            $js .= "f.codSetor.options[0] = new Option('Selecione','xxx'".$default."); \n";
            $js .= "limpaSelect(f.codLocal,0); \n";
            $js .= "f.codLocal.options[0] = new Option('Selecione','xxx'".$default."); \n";
            $dbEmp->limpaSelecao();
            $dbEmp->fechaBD();
        } else {
            $js = "limpaSelect(f.codUnidade,0); \n";
            $js .= "f.codUnidade.options[0] = new Option('Selecione','xxx'".$default."); \n";
            $js .= "limpaSelect(f.codDepartamento,0); \n";
            $js .= "f.codDepartamento.options[0] = new Option('Selecione','xxx'".$default."); \n";
            $js .= "limpaSelect(f.codSetor,0); \n";
            $js .= "f.codSetor.options[0] = new Option('Selecione','xxx'".$default."); \n";
            $js .= "limpaSelect(f.codLocal,0); \n";
            $js .= "f.codLocal.options[0] = new Option('Selecione','xxx'".$default."); \n";
            $codMasSetor = validaMascaraDinamica($mascaraSetor,"0");
            $js .= "f.codMasSetor.value = \"".$codMasSetor[1]."\";\n";
        }
    break;

    case 'codUnidade':
        if ($codUnidade != "xxx") {
            $variaveis = explode("-",$codUnidade);
            $codUnidaded = $variaveis[0];
            $codOrgaod = $variaveis[1];
            $anoExercicio = $variaveis[2];
            //Faz o combo de Órgãos
            $sSQL  = "SELECT cod_departamento, nom_departamento FROM ";
            $sSQL .= "administracao.departamento WHERE cod_unidade = ".$codUnidaded;
            $sSQL .= " AND cod_orgao = ".$codOrgaod;
            $sSQL .= " AND ano_exercicio = '".$anoExercicio."' ";
            $sSQL .= " ORDER by nom_departamento";

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
                $chaveD = $codDepartamentof."-".$codUnidaded."-".$codOrgaod."-".$anoExercicio;
                $dbEmp->vaiProximo();
                $js .= "f.codDepartamento.options[$cont] = new Option('".$nomDepartamentof."','".$chaveD."'".$default."); ";
                $cont++;
            }
            $js .= "f.codDepartamento.options[0].selected = true; \n";
            $js .= "limpaSelect(f.codSetor,0); \n";
            $js .= "f.codSetor.options[0] = new Option('Selecione','xxx'".$default."); \n";
            $js .= "limpaSelect(f.codLocal,0); \n";
            $js .= "f.codLocal.options[0] = new Option('Selecione','xxx'".$default."); \n";
            $dbEmp->limpaSelecao();
            $dbEmp->fechaBD();
        } else {
            $js = "limpaSelect(f.codDepartamento,0); \n";
            $js .= "f.codDepartamento.options[0] = new Option('Selecione','xxx'".$default."); \n";
            $js .= "limpaSelect(f.codSetor,0); \n";
            $js .= "f.codSetor.options[0] = new Option('Selecione','xxx'".$default."); \n";
            $js .= "limpaSelect(f.codLocal,0); \n";
            $js .= "f.codLocal.options[0] = new Option('Selecione','xxx'".$default."); \n";
            $variaveis = explode("-","0-".$codOrgao);
            $codUnidaded = $variaveis[0];
            $codOrgaod = $variaveis[1];
            $codMasSetor = validaMascaraDinamica($mascaraSetor, $codOrgaod."-".$codUnidaded);
            $js .= "f.codMasSetor.value = \"".$codMasSetor[1]."\";\n";
        }
    break;

    case 'codDepartamento':
        if ($codDepartamento != "xxx") {
            $variaveis = explode("-",$codDepartamento);
            $codDepartamentoS = $variaveis[0];
            $codUnidadeS = $variaveis[1];
            $codOrgaoS = $variaveis[2];
            $anoExercicio = $variaveis[3];
            //Faz o combo de Órgãos
            $sSQL  = "SELECT cod_setor, nom_setor FROM administracao.setor WHERE ";
            $sSQL .= "cod_departamento = ".$codDepartamentoS." AND ";
            $sSQL .= "cod_unidade = ".$codUnidadeS." AND cod_orgao = ".$codOrgaoS." ";
            $sSQL .= " AND ano_exercicio = '".$anoExercicio."' ";
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
            $js .= "limpaSelect(f.codLocal,0); \n";
            $js .= "f.codLocal.options[0] = new Option('Selecione','xxx'".$default."); \n";
            while (!$dbEmp->eof()) {
                $codSetorf  = trim($dbEmp->pegaCampo("cod_setor"));
                $nomSetorf  = trim($dbEmp->pegaCampo("nom_setor"));
                $chaveS = $codSetorf."-".$codDepartamentoS."-".$codUnidadeS."-".$codOrgaoS."-".$anoExercicio;
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
            $js .= "limpaSelect(f.codLocal,0); \n";
            $js .= "f.codLocal.options[0] = new Option('Selecione','xxx'".$default."); \n";
            $variaveis = explode("-","0-".$codUnidade);
            $codDepartamentoS = $variaveis[0];
            $codUnidadeS = $variaveis[1];
            $codOrgaoS = $variaveis[2];
            $codMasSetor = validaMascaraDinamica($mascaraSetor,$codOrgaoS."-".$codUnidadeS."-".$codDepartamentoS);
            $js .= "f.codMasSetor.value = \"".$codMasSetor[1]."\";\n";
        }
    break;

    case 'codSetor':
        if ($codSetor != "xxx") {
            $variaveis = explode("-",$codSetor);
            $codSetorS = $variaveis[0];
            $codDepartamentoS = $variaveis[1];
            $codUnidadeS = $variaveis[2];
            $codOrgaoS = $variaveis[3];
            $anoExercicio = $variaveis[4];

            //Faz o combo de Órgãos
            $sSQL  = "SELECT cod_local, nom_local, ano_exercicio FROM administracao.local WHERE ";
            $sSQL .= "cod_setor = ".$codSetorS." AND ";
            $sSQL .= "cod_departamento = ".$codDepartamentoS." AND ";
            $sSQL .= "cod_unidade = ".$codUnidadeS." AND cod_orgao = ".$codOrgaoS." ";
            $sSQL .= " AND ano_exercicio = '".$anoExercicio."' ";
            $sSQL .= " ORDER by nom_local";
            $dbEmp = new dataBaseLegado;
            $dbEmp->abreBD();
            $dbEmp->abreSelecao($sSQL);
            $dbEmp->vaiPrimeiro();
            $cont = 1;
            $js = "";
            $codMasSetor = validaMascaraDinamica($mascaraSetor,$codOrgaoS."-".$codUnidadeS."-".$codDepartamentoS."-".$codSetorS);
            $js .= "f.codMasSetor.value = \"".$codMasSetor[1]."\";\n";
            $js .= "limpaSelect(f.codLocal,0); \n";
            $js .= "f.codLocal.options[0] = new Option('Selecione','xxx'".$default."); \n";
            while (!$dbEmp->eof()) {
                $codLocalf  = trim($dbEmp->pegaCampo("cod_local"));
                $nomLocalf  = trim($dbEmp->pegaCampo("nom_local"));
                $anoExercicioLocal  = trim($dbEmp->pegaCampo("ano_exercicio"));
                $chaveS = $codLocalf."-".$codSetorS."-".$codDepartamentoS."-".$codUnidadeS."-".$codOrgaoS."-".$anoExercicioLocal;
                $dbEmp->vaiProximo();
                $js .= "f.codLocal.options[$cont] = new Option('".$nomLocalf  ."','".$chaveS."'".$default."); \n";
                $cont++;
            }
            $js .= "f.codLocal.options[0].selected = true \n";
            $dbEmp->limpaSelecao();
            $dbEmp->fechaBD();
        } else {
            $js .= "limpaSelect(f.codLocal,0); \n";
            $js .= "f.codLocal.options[0] = new Option('Selecione','xxx'".$default."); \n";

            $variaveis = explode("-","0-".$codDepartamento);
            $codSetorS = $variaveis[0];
            $codDepartamentoS = $variaveis[1];
            $codUnidadeS = $variaveis[2];
            $codOrgaoS = $variaveis[3];
            $codLocal = 0;
            $codMasSetor = validaMascaraDinamica($mascaraSetor,$codOrgaoS."-".$codUnidadeS."-".$codDepartamentoS."-".$codSetorS."-".$codLocal);

            $js .= "f.codMasSetor.value = \"".$codMasSetor[1]."\";\n";
        }

    break;

    case 'codLocal':
        if ($codLocal == "xxx") {
            $variaveis = preg_split( "/[^a-zA-Z0-9]/", $codMasSetor );
            $codSetorS         = $variaveis[3];
            $codDepartamentoS  = $variaveis[2];
            $codUnidadeS       = $variaveis[1];
            $codOrgaoS         = $variaveis[0];
            $codLocalS         = '0';
            $anoExercicioLocal = '0000';
        } else {
            $variaveis = explode("-",$codLocal);
            $codLocalS         = $variaveis[0];
            $codSetorS         = $variaveis[1];
            $codDepartamentoS  = $variaveis[2];
            $codUnidadeS       = $variaveis[3];
            $codOrgaoS         = $variaveis[4];
            $anoExercicioLocal = $variaveis[5];
        }
        $codMasSetor = validaMascaraDinamica($mascaraSetor,$codOrgaoS."-".$codUnidadeS."-".$codDepartamentoS."-".$codSetorS."-".$codLocalS."/".$anoExercicioLocal);
        $js .= "if (f.ok) {                                     \n";
        $js .= "   if (f.ok.disabled==true) {                   \n";
        $js .= "      f.ok.disabled=false;                    \n";
        $js .= "}                                             \n";
        $js .= "   }                                          \n";
        $js .= "f.codMasSetor.value = \"".$codMasSetor[1]."\";\n";
    break;

    case 'descricao':
        if ($codLocal != 'xxx') {
            $descricao = explode('-',$codLocal);
            $codOrgao = str_pad($descricao[4],2,0,STR_PAD_LEFT);
            $codUnidade = str_pad($descricao[3],3,0,STR_PAD_LEFT);
            $codDepartamento = str_pad($descricao[2],3,0,STR_PAD_LEFT);
            $codSetor = str_pad($descricao[1],3,0,STR_PAD_LEFT);
            $codLocal = str_pad($descricao[0],3,0,STR_PAD_LEFT);
            $exercicioDescricao = $descricao[5];
            $codMasSetor = $codOrgao.".".$codUnidade.".".$codDepartamento.".".$codSetor.".".$codLocal."/".$exercicioDescricao;
            //select para pegar nome do orgão
            $sSQL = "SELECT  nom_orgao from administracao.orgao where cod_orgao = '".$codOrgao."' and ano_exercicio = '".$exercicioDescricao."';";
            $dbOrg = new dataBaseLegado;
            $dbOrg->abreBD();
            $dbOrg->abreSelecao($sSQL);
            $dbOrg->vaiPrimeiro();
            while (!$dbOrg->eof()) {
                $nom_orgao =  $dbOrg->pegaCampo("nom_orgao")."-".$exercicioDescricao;
                $dbOrg->vaiProximo();
            }
            $dbOrg->limpaSelecao();
            $dbOrg->fechaBd();
            //select para pegar o nome da unidade
            $sSQL = "SELECT  nom_unidade from administracao.unidade where cod_orgao = '".$codOrgao."' and cod_unidade = '".$codUnidade."' and  ano_exercicio = '".$exercicioDescricao."';";
            $dbOrg = new dataBaseLegado;
            $dbOrg->abreBD();
            $dbOrg->abreSelecao($sSQL);
            $dbOrg->vaiPrimeiro();
            while (!$dbOrg->eof()) {
                $nom_unidade =  $dbOrg->pegaCampo("nom_unidade");
                $dbOrg->vaiProximo();
            }
            $dbOrg->limpaSelecao();
            $dbOrg->fechaBd();
            //select para pegar o nome do departamento
            $sSQL = "SELECT  nom_departamento from administracao.departamento where cod_orgao = '".$codOrgao."' and cod_unidade = '".$codUnidade."' and cod_departamento = '".$codDepartamento."'  and  ano_exercicio = '".$exercicioDescricao."';";
            $dbOrg = new dataBaseLegado;
            $dbOrg->abreBD();
            $dbOrg->abreSelecao($sSQL);
            $dbOrg->vaiPrimeiro();
            while (!$dbOrg->eof()) {
                $nom_departamento =  $dbOrg->pegaCampo("nom_departamento");
                $dbOrg->vaiProximo();
            }
            $dbOrg->limpaSelecao();
            $dbOrg->fechaBd();
            //select para pegar o nome do setor
            $sSQL = "SELECT  nom_setor from administracao.setor where cod_orgao = '".$codOrgao."' and cod_unidade = '".$codUnidade."' and cod_departamento = '".$codDepartamento."' and cod_setor = '".$codSetor."' and  ano_exercicio = '".$exercicioDescricao."';";
            $dbOrg = new dataBaseLegado;
            $dbOrg->abreBD();
            $dbOrg->abreSelecao($sSQL);
            $dbOrg->vaiPrimeiro();
            while (!$dbOrg->eof()) {
                $nom_setor =  $dbOrg->pegaCampo("nom_setor");
                $dbOrg->vaiProximo();
            }
            $dbOrg->limpaSelecao();
            $dbOrg->fechaBd();
            //select para pegar o nome do local
            $sSQL = "SELECT  nom_local from administracao.local where cod_orgao = '".$codOrgao."' and cod_unidade = '".$codUnidade."' and cod_departamento = '".$codDepartamento."' and cod_setor =
                    '".$codSetor."' and cod_local = '".$codLocal."' and  ano_exercicio = '".$exercicioDescricao."';";
            $dbOrg = new dataBaseLegado;
            $dbOrg->abreBD();
            $dbOrg->abreSelecao($sSQL);
            $dbOrg->vaiPrimeiro();
            while (!$dbOrg->eof()) {
                $nom_local =  $dbOrg->pegaCampo("nom_local");
                $dbOrg->vaiProximo();
            }
            $dbOrg->limpaSelecao();
            $dbOrg->fechaBd();

            $js .= "f.nomOrgao.value = \"".$nom_orgao."\";\n";
            $js .= "f.nomUnidade.value = \"".$nom_unidade."\";\n";
            $js .= "f.nomDepartamento.value = \"".$nom_departamento."\";\n";
            $js .= "f.nomSetor.value = \"".$nom_setor."\";\n";
            $js .= "f.nomLocal.value = \"".$nom_local."\";\n";
            $js .= "f.codMasSetor.value = \"".$codMasSetor."\";\n";
        } else {
            $variaveis = preg_split( "/[^a-zA-Z0-9]/", $codMasSetor );
            $codSetorS         = $variaveis[3];
            $codDepartamentoS  = $variaveis[2];
            $codUnidadeS       = $variaveis[1];
            $codOrgaoS         = $variaveis[0];
            $codLocalS         = '0';
            $anoExercicioLocal = '0000';
            $codMasSetor = validaMascaraDinamica($mascaraSetor,$codOrgaoS."-".$codUnidadeS."-".$codDepartamentoS."-".$codSetorS."-".$codLocalS."/".$anoExercicioLocal);
            $js .= "f.codMasSetor.value = \"".$codMasSetor[1]."\";\n";
        }
    break;
    }

    executaFrameOculto($js);

?>
