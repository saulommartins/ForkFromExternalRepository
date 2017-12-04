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
    * Oculto de Relatório de Concessão de Vale-Tranporte
    * Data de Criação: 07/11/2005

    * @author Desenvolvedor: Andre Almeida

    * Casos de uso: uc-04.04.00

    $Id: OCFiltroCGM.php 63818 2015-10-19 20:02:07Z evandro $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GRH_PES_NEGOCIO."RPessoalServidor.class.php" );
include_once ( CAM_GA_CGM_NEGOCIO."RCGMPessoaFisica.class.php" );
include_once ( CAM_GRH_PES_COMPONENTES."IContratoDigitoVerificador.class.php");

function buscaCGM()
{
    $obRCGMPessoaFisica = new RCGMPessoaFisica;
    $obRPessoalServidor = new RPessoalServidor;
    $stJs = "";
    $boTransacao = "";
    $boValidaCgmAtivos = Sessao::read('valida_ativos_cgm');

    $campoNum = (isset($_GET['campoNum']))?$_GET['campoNum']:"inNumCGM";
    $campoNom = (isset($_GET['campoNom']))?$_GET['campoNom']:"inCampoInner";

    if ($_GET['inNumCGM'] != '') {
        $obRCGMPessoaFisica->setNumCGM( $_GET['inNumCGM'] );
        $obRCGMPessoaFisica->consultarCGM( $rsCGMPessoaFisica );
        $boErro = false;
        if ( $rsCGMPessoaFisica->getNumLinhas() <= 0 or $obRCGMPessoaFisica->getNumCGM() == 0  ) {
            $stJs .= "alertaAviso('@CGM não encontrado. (".$_GET['inNumCGM'].")','form','erro','".Sessao::getId()."');";
            $stJs .= 'f.'.$campoNum.'.value = "";';
            $stJs .= 'f.'.$campoNom.'.focus();';
            $stJs .= 'd.getElementById("'.$campoNom.'").innerHTML = "&nbsp;&nbsp;";';
            $boErro = true;
        }
        if ( $obRCGMPessoaFisica->getNumCGM() and !$boErro ) {
            $obRPessoalServidor->obRCGMPessoaFisica->setNumCGM( $_GET['inNumCGM'] );
            $obRPessoalServidor->addContratoServidor();
            if ($boValidaCgmAtivos == 'true') {
                $obRPessoalServidor->recuperaCgmDoRegistro( $rsServidor, '','', $boTransacao );
            }else{
                $obRPessoalServidor->consultaCGMServidor( $rsServidor, "", $boTransacao );
            }
            include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalPensionista.class.php");
            $obTPessoalPensionista = new TPessoalPensionista();
            $stFiltro = " WHERE numcgm = ".trim($_GET['inNumCGM']);
            $obTPessoalPensionista->recuperaTodos($rsPensionista,$stFiltro);

            if ( $rsServidor->getNumLinhas() <= 0 and $rsPensionista->getNumLinhas() <= 0) {
                $stJs .= "alertaAviso('@CGM ".$_GET['inNumCGM']." não cadastrado como servidor ou pensionista.','form','erro','".Sessao::getId()."');\n";
                $stJs .= "f.".$campoNum.".value = '';\n";
                $stJs .= "f.".$campoNom.".focus();\n";
                $stJs .= 'd.getElementById("'.$campoNom.'").innerHTML = "&nbsp;&nbsp;";';
                $boErro = true;
            } else {
               $stJs .= "d.getElementById('".$campoNom."').innerHTML = '".addslashes($rsCGMPessoaFisica->getCampo('nom_cgm'))."';";
               $stJs .= "f.".$campoNom.".value = '".addslashes($rsCGMPessoaFisica->getCampo('nom_cgm'))."';\n";
            }
        }
    } else {
        $stJs .= "f.".$campoNum.".value = '';                                    \n";
        $stJs .= "f.".$campoNom.".focus();                                   \n";
        $stJs .= 'd.getElementById("'.$campoNom.'").innerHTML = "&nbsp;&nbsp;";';
    }

    return $stJs;
}

function buscaCGMPensionista()
{
    $obRCGMPessoaFisica = new RCGMPessoaFisica;
    $obRPessoalServidor = new RPessoalServidor;
    if ($_GET['inNumCGMPensionista'] != '') {
        $obRCGMPessoaFisica->setNumCGM( $_GET['inNumCGMPensionista'] );
        $obRCGMPessoaFisica->consultarCGM( $rsCGMPessoaFisica );
        $boErro = false;
        if ( $rsCGMPessoaFisica->getNumLinhas() <= 0 or $obRCGMPessoaFisica->getNumCGM() == 0  ) {
            $stJs .= "alertaAviso('@CGM não encontrado. (".$_GET['inNumCGMPensionista'].")','form','erro','".Sessao::getId()."');";
            $stJs .= 'f.inNumCGMPensionista.value = "";';
            $stJs .= 'f.inCampoInnerPensionista.focus();';
            $stJs .= 'd.getElementById("inCampoInnerPensionista").innerHTML = "&nbsp;&nbsp;";';
            $boErro = true;
        }
        if ( $obRCGMPessoaFisica->getNumCGM() and !$boErro ) {
            include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalPensionista.class.php");
            $obTPessoalPensionista = new TPessoalPensionista();
            $stFiltro = " WHERE numcgm = ".$_GET['inNumCGMPensionista'];
            $obTPessoalPensionista->recuperaTodos($rsPensionista,$stFiltro);
            if ( $rsPensionista->getNumLinhas() <= 0 ) {
                $stJs .= "alertaAviso('@CGM ".$_GET['inNumCGMPensionista']." não cadastrado como pensionista.','form','erro','".Sessao::getId()."');\n";
                $stJs .= "f.inNumCGMPensionista.value = '';\n";
                $stJs .= "f.inCampoInnerPensionista.focus();\n";
                $stJs .= 'd.getElementById("inCampoInnerPensionista").innerHTML = "&nbsp;&nbsp;";';
                $boErro = true;
            } else {
               $stJs .= "d.getElementById('inCampoInnerPensionista').innerHTML = '".addslashes($rsCGMPessoaFisica->getCampo('nom_cgm'))."';";
               $stJs .= "f.inCampoInnerPensionista.value = '".addslashes($rsCGMPessoaFisica->getCampo('nom_cgm'))."';\n";
            }
        }
    } else {
        $stJs .= "f.inNumCGMPensionista.value = '';                                    \n";
        $stJs .= "f.inCampoInnerPensionista.focus();                                   \n";
        $stJs .= 'd.getElementById("inCampoInnerPensionista").innerHTML = "&nbsp;";    \n';
    }

    return $stJs;
}

function montaContrato($boRescindido)
{
    $obRCGMPessoaFisica = new RCGMPessoaFisica;
    $obRPessoalServidor = new RPessoalServidor;
    $stJs = "";
    $boTransacao = "";
    $boValidaCgmAtivos = Sessao::read('valida_ativos_cgm');

    if ($_GET['inNumCGM'] != '') {
        $obRCGMPessoaFisica->setNumCGM( $_GET['inNumCGM'] );
        $obRCGMPessoaFisica->consultarCGM( $rsCGMPessoaFisica );
        $boErro = false;
        if ( $rsCGMPessoaFisica->getNumLinhas() <= 0 or $obRCGMPessoaFisica->getNumCGM() == 0  ) {
            $stJs .= "limpaSelect(f.inContrato,0);                              \n";
            $stJs .= "f.inContrato[0] = new Option('Selecione','','selected');  \n";
            $boErro = true;
        }
        if ( $obRCGMPessoaFisica->getNumCGM() and !$boErro ) {
            $obRPessoalServidor->obRCGMPessoaFisica->setNumCGM( $_GET['inNumCGM'] );
            $obRPessoalServidor->addContratoServidor();
            if ($boValidaCgmAtivos == 'true') {
                $obRPessoalServidor->recuperaCgmDoRegistro( $rsServidor, '','', $boTransacao );
            }else{
                $obRPessoalServidor->consultaCGMServidor( $rsServidor, "", $boTransacao );
            }

            include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalPensionista.class.php");
            $obTPessoalPensionista = new TPessoalPensionista();
            $stFiltro = " WHERE numcgm = ".trim($_GET['inNumCGM']);
            $obTPessoalPensionista->recuperaTodos($rsPensionista,$stFiltro);

            if ( $rsServidor->getNumLinhas() <= 0 AND $rsPensionista->getNumLinhas() <= 0) {
                $stJs .= "limpaSelect(f.inContrato,0);                              \n";
                $stJs .= "f.inContrato[0] = new Option('Selecione','','selected');  \n";
                $boErro = true;
            } else {
                include_once ( CAM_GRH_PES_MAPEAMENTO."TPessoalServidor.class.php");
                $obTPessoalServidor = new TPessoalServidor;
                $stFiltro  = " AND ps.numcgm = ".$_GET['inNumCGM']."   \n";
                if ($boRescindido == false) {
                    $stFiltro .= " AND pc.cod_contrato NOT IN (                                 \n";
                    $stFiltro .= "    SELECT                                                    \n";
                    $stFiltro .= "        cod_contrato                                          \n";
                    $stFiltro .= "    FROM                                                      \n";
                    $stFiltro .= "        pessoal.contrato_servidor_caso_causa )                \n";
                }
                if($boValidaCgmAtivos == 'true'){
                    $stFiltro .= " AND recuperarSituacaoDoContratoLiteral(pc.cod_contrato, 0, '".Sessao::getEntidade()."') = 'Ativo' ";
                }
               $obErro = $obTPessoalServidor->recuperaRegistrosServidor( $rsRegistros, $stFiltro );
               $stJs .= "limpaSelect(f.inContrato,0);\n";
               $stJs .= "f.inContrato[0] = new Option('Selecione','','selected');\n";
               $inIndex = 1;
               while ( !$rsRegistros->eof() ) {
                   $stJs .= "f.inContrato[".$inIndex."] = new Option('".$rsRegistros->getCampo('registro')."','".$rsRegistros->getCampo('registro')."','');\n";
                   $inIndex++;
                   $rsRegistros->proximo();
               }
                include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalContratoPensionista.class.php");
                $obTPessoalContratoPensionista = new TPessoalContratoPensionista();
                $stFiltro = " AND numcgm = ".$_GET['inNumCGM'];
                $obTPessoalContratoPensionista->recuperaRelacionamento($rsRegistros,$stFiltro);
               while ( !$rsRegistros->eof() ) {
                   $stJs .= "f.inContrato[".$inIndex."] = new Option('".$rsRegistros->getCampo('registro')."','".$rsRegistros->getCampo('registro')."','');\n";
                   $inIndex++;
                   $rsRegistros->proximo();
               }
            }
        }
    } else {
        $stJs .= "limpaSelect(f.inContrato,0);                              \n";
        $stJs .= "f.inContrato[0] = new Option('Selecione','','selected');  \n";
    }

    return $stJs;
}

function montaPensionista()
{
    $stJs .= "limpaSelect(f.inContratoPensionista,0);                              \n";
    $stJs .= "f.inContratoPensionista[0] = new Option('Selecione','','selected');  \n";
    if ($_GET['inNumCGMPensionista'] != '') {
        include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalContratoPensionista.class.php");
        $obTPessoalContratoPensionista = new TPessoalContratoPensionista();
        $stFiltro = " AND numcgm = ".$_GET['inNumCGMPensionista'];
        $obTPessoalContratoPensionista->recuperaRelacionamento($rsContratos,$stFiltro);
        $inIndex = 1;
        while ( !$rsContratos->eof() ) {
            $stJs .= "f.inContratoPensionista[".$inIndex."] = new Option('".$rsContratos->getCampo('registro')."','".$rsContratos->getCampo('registro')."','');\n";
            $inIndex++;
            $rsContratos->proximo();
        }
    }

    return $stJs;
}

function preencheCGMContrato($boExecuta=false,$boExtendido=false)
{
    $boTransacao = $stJs = "";
    if ($_GET['inContrato']) {
        $obRPessoalServidor = new RPessoalServidor;
        $obRCGMPessoaFisica = new RCGMPessoaFisica;
        $obRPessoalServidor->addContratoServidor();
        $obRPessoalServidor->roUltimoContratoServidor->listarCgmDoRegistro($rsCGM,$_GET['inContrato'],$boTransacao);
        if ( $rsCGM->getNumLinhas() > 0 ) {
            include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalContratoServidorCasoCausa.class.php");
            $obTPessoalContratoServidorCasoCausa = new TPessoalContratoServidorCasoCausa();
            $stFiltro = " WHERE cod_contrato = ".$rsCGM->getCampo("cod_contrato");
            $obTPessoalContratoServidorCasoCausa->recuperaTodos($rsRescisao,$stFiltro);
            if ( $rsRescisao->getNumLinhas() > 0 and $_GET['stSituacao'] == 'ativos' ) {
                $stJs .= "d.getElementById('inNomCGM').innerHTML = '&nbsp;'; \n";
                $stJs .= "d.getElementById('inContrato').value = '';\n";
                $stJs .= "alertaAviso('@Matrícula está rescindida. (".$_GET['inContrato'].")','form','erro','".Sessao::getId()."');\n";
            } else {
                $stNomCGM = $rsCGM->getCampo('numcgm') ." - ". $rsCGM->getCampo('nom_cgm');
                $stJs .= "d.getElementById('inNomCGM').innerHTML = '".addslashes($stNomCGM)."';       \n";
                $stJs .= "f.hdnCGM.value = '".addslashes($stNomCGM)."';                               \n";
                if ($boExtendido) {
                    $stJs .= preencheInformacoesFuncao( $_GET['inContrato'] );
                }
            }
            
            include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalContratoPensionista.class.php");
            $obTPessoalContratoPensionista = new TPessoalContratoPensionista;
            $obTPessoalContratoPensionista->recuperaTodos($rsPensionista, ' WHERE cod_contrato = '.$rsCGM->getCampo("cod_contrato"));
            
            if($rsPensionista->getNumLinhas() > 0) {
               $stJs .= "d.getElementById('inContratoPensionista').value = '".$rsPensionista->getCampo('cod_contrato')."';\n";
            }else{
                $stJs .= "d.getElementById('inContratoPensionista').value = '';\n";
            }
            
        } else {
            if ($boExtendido) {
                $stJs .= preencheInformacoesFuncao( $_GET['inContrato'] );
            }
            $stJs .= "d.getElementById('inNomCGM').innerHTML = '&nbsp;';                                \n";
            $stJs .= "d.getElementById('inContrato').value = '';                                        \n";
            $stJs .= "alertaAviso('@Matrícula informada não existe. (".$_GET['inContrato'].")','form','erro','".Sessao::getId()."');   \n";
            $stJs .= "d.getElementById('inContrato').focus();\n";
        }
    } else {
        if ($_GET['inContrato'] == '0') {
            $stJs .= "d.getElementById('inContrato').value = '';\n";
            $stJs .= "alertaAviso('@Matrícula informada não existe. (".$_GET['inContrato'].")','form','erro','".Sessao::getId()."');   \n";
            $stJs .= "d.getElementById('inContrato').focus();\n";
        }

        $stJs .= "d.getElementById('inNomCGM').innerHTML = '&nbsp;'; \n";
        if ($boExtendido) {
            $stJs .= "d.getElementById('stInformacoesFuncao').innerHTML = '&nbsp;'; \n";
        }
    }

    return $stJs;
}

function preencheCGMPensionista()
{
    if ($_GET['inContratoPensionista']) {
        include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalPensionista.class.php");
        $obTPessoalPensionista = new TPessoalPensionista();
        $stFiltro = " AND registro = ".$_GET['inContratoPensionista'];
        $obTPessoalPensionista->recuperaCgmDoRegistro($rsCGM,$stFiltro);
        if ( $rsCGM->getNumLinhas() > 0 ) {
            $stNomCGM = $rsCGM->getCampo('numcgm') ." - ". $rsCGM->getCampo('nom_cgm');
            $stJs .= "d.getElementById('inNomCGMPensionista').innerHTML = '".addslashes($stNomCGM)."';       \n";
            $stJs .= "f.hdnCGMPensionista.value = '".addslashes($stNomCGM)."';                               \n";
        } else {
            $stJs .= "d.getElementById('inNomCGMPensionista').innerHTML = '&nbsp;';                                \n";
            $stJs .= "d.getElementById('inContratoPensionista').value = '';                                        \n";
            $stJs .= "alertaAviso('@Matrícula informada do pensionista não existe. (".$_GET['inContratoPensionista'].")','form','erro','".Sessao::getId()."');   \n";
        }
    } else {
        $stJs .= "d.getElementById('inNomCGMPensionista').innerHTML = '&nbsp;'; \n";
        $stJs .= "f.inContratoPensionista.value = ''; \n";
    }

    return $stJs;
}

function preencheInformacoesFuncao($inContrato)
{
    $obRPessoalServidor = new RPessoalServidor;
    $obRPessoalServidor->addContratoServidor();
    $obRPessoalServidor->roUltimoContratoServidor->listarFuncaoDoRegistro($rsFuncao,$inContrato,$boTransacao);
    $stInformacoesFuncao = $rsFuncao->getCampo('descricao') . " - " . $rsFuncao->getCampo('dt_posse');
    if ( $inContrato != "" and $rsFuncao->getNumLinhas() > 0 ) {
        $stJs .= "d.getElementById('stInformacoesFuncao').innerHTML = '".$stInformacoesFuncao."';   \n";
    } else {
        $stJs .= "d.getElementById('stInformacoesFuncao').innerHTML = '&nbsp;';                     \n";
    }

    return $stJs;
}

switch ($_GET['stCtrl']) {
    case "preencheCGMContrato":
        $stJs = preencheCGMContrato();
    break;
    case "preencheCGMPensionista":
        $stJs = preencheCGMPensionista();
    break;
    case "preencheCGMContratoExtendido":
        $stJs = preencheCGMContrato(false,true);
    break;
    case "buscaCGMContrato":
        $stJs = buscaCGM();
        if( $_GET['boPreencheCombo'] ) $stJs .= montaContrato($_GET['boRescindido']);
    break;
    case "montaContrato":
        if( $_GET['boPreencheCombo'] ) $stJs .= montaContrato($_GET['boRescindido']);
    break;
    case "buscaCGMPensionista":
        $stJs .= buscaCGMPensionista();
        $stJs .= montaPensionista();
    break;
    case "preencheInformacoesFuncao":
        $stJs .= preencheInformacoesFuncao( $_GET['inContrato'] );
    break;
}
if ($stJs) {
    echo $stJs;
}
?>
