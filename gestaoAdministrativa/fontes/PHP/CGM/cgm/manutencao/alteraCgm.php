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
    * Arquivo de manutenção de CGM
    * Data de Criação: 25/07/2005

    * @author Analista: Cassiano
    * @author Desenvolvedor: Cassiano

    Casos de uso: uc-01.02.92, uc-01.02.93

    $Id: alteraCgm.php 63446 2015-08-28 15:00:01Z michel $

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_FW_LEGADO."funcoesLegado.lib.php";
include_once CAM_FW_LEGADO."cgmLegado.class.php"; //Insere a classe que manipula os dados do CGM
include_once CAM_FW_LEGADO."auditoriaLegada.class.php"; //Inclui classe para inserir auditoria
include_once 'interfaceCgm.class.php'; //Insere a classe que constroi a interface html para CGM

setAjuda('UC-01.02.92');
if (!isset($_REQUEST["controle"])) {
    $controle = 0;
} else {
    $controle = $_REQUEST["controle"];
}

?>
<script src="../../../../../../gestaoAdministrativa/fontes/javaScript/ifuncoesJs.js" type="text/javascript"></script>
<script src="../../../../../../gestaoAdministrativa/fontes/javaScript/funcoesJs.js" type="text/javascript"></script>
<?php
$html = new interfaceCgm;

$numCgm = $_REQUEST['numCgm'];

switch ($controle) {
    case 600: // Valida o cpf/cnpj para ver se o mesmo pertence a outro cgm.
        $pessoa = $_REQUEST["pessoa"];
        $tipo   = $_REQUEST["tipo"];
        $cpf    = $_REQUEST["cpf"];
        $cnpj   = $_REQUEST["cnpj"];
        if (($pessoa == "fisica") or ($pessoa == "outros" && $tipo == "fisica")) {
            if (!(comparaValor("cpf", preg_replace('/[^a-zA-Z0-9]/','', $cpf ), "sw_cgm_pessoa_fisica", "and numcgm <> $_REQUEST[numCgm]"))) {
                $stJs = "alertaAviso('@O CPF digitado já foi cadastrado. (".$_REQUEST["cpf"].")', 'form','erro','".Sessao::getId()."');";
                $stJs .= 'f.cpf.value = "";';
                $stJs .= 'f.cpf.focus();';
                SistemaLegado::executaFrameOculto($stJs);
            }
        } elseif (($pessoa == "juridica") or ($pessoa == "outros" && $tipo == "juridica")) {
            if (!(comparaValor("cnpj/", preg_replace( '/[^a-zA-Z0-9]/','', $cnpj ), "sw_cgm_pessoa_juridica", "and numcgm <> $_REQUEST[numCgm]"))) {
                    $stJs = "alertaAviso('@O CNPJ digitado já foi cadastrado. (".$_REQUEST["cnpj"].")', 'form','erro','".Sessao::getId()."');";
                    $stJs .= 'f.cnpj.value = "";';
                    $stJs .= 'f.cnpj.focus();';
                    SistemaLegado::executaFrameOculto($stJs);
            }
        }
    break;
    case 667:
        include_once ( CAM_GT_CIM_NEGOCIO."RCIMTrecho.class.php" );

        $obRCIMTrecho       = new RCIMTrecho;
        $rsLogradouro       = new RecordSet;

        if ( empty( $_REQUEST["inNumLogradouroCorresp"] ) || empty( $_REQUEST["paisCorresp"] ) ) {
            $stJs .= 'd.getElementById("campoInnerLogrCorresp").innerHTML = "&nbsp;";';
            $stJs .= 'd.getElementById("stMunicipioCorresp").innerHTML = "&nbsp;";';
            $stJs .= 'd.getElementById("stEstadoCorresp").innerHTML = "&nbsp;";';
            $stJs .= "limpaSelect(f.cmbBairroCorresp,0); \n\r";
            $stJs .= "limpaSelect(f.cmbCEPCorresp,0); \n\r";
            $stJs .= "f.cmbBairroCorresp[0] = new Option('Selecione','', 'selected');\n\r";
            $stJs .= "f.cmbCEPCorresp[0] = new Option('Selecione','', 'selected');\n\r";
        } else {
            $obRCIMTrecho->setCodigoLogradouro( $_REQUEST["inNumLogradouroCorresp"] ) ;
            $obRCIMTrecho->listarLogradourosTrecho( $rsLogradouro, "", $_REQUEST["paisCorresp"] );
            if ( $rsLogradouro->eof() ) {
                $stJs .= 'f.inNumLogradouroCorresp.value = "";';
                $stJs .= 'f.inNumLogradouroCorresp.focus();';
                $stJs .= 'd.getElementById("campoInnerLogrCorresp").innerHTML = "&nbsp;";';
                $stJs .= 'd.getElementById("stMunicipioCorresp").innerHTML = "&nbsp;";';
                $stJs .= 'd.getElementById("stEstadoCorresp").innerHTML = "&nbsp;";';

                $stJs .= "limpaSelect(f.cmbBairroCorresp,0); \n\r";
                $stJs .= "limpaSelect(f.cmbCEPCorresp,0); \n\r";
                $stJs .= "f.cmbBairroCorresp[0] = new Option('Selecione','', 'selected');\n\r";
                $stJs .= "f.cmbCEPCorresp[0] = new Option('Selecione','', 'selected');\n\r";

                $stJs .= "alertaAviso('@Valor inválido. (".$_REQUEST["inNumLogradouroCorresp"].")', 'form','erro','".Sessao::getId()."');";
            } else {
                $stNomeLogradouro = $rsLogradouro->getCampo ("tipo_nome");
                $stJs .= 'd.getElementById("campoInnerLogrCorresp").innerHTML = "'.$stNomeLogradouro.'";';

                $stNomeMunicipio = $rsLogradouro->getCampo ("cod_municipio") . ' - ' .$rsLogradouro->getCampo ("nom_municipio");
                $stNomeEstado = $rsLogradouro->getCampo ("cod_uf") . ' - ' .$rsLogradouro->getCampo ("nom_uf");

                $stJs .= "f.stNomeLogradouroCorresp.value = '". $stNomeLogradouro."';";
                $stJs .= 'd.getElementById("stMunicipioCorresp").innerHTML = "'.$stNomeMunicipio.'";';
                $stJs .= 'd.getElementById("stEstadoCorresp").innerHTML = "'.$stNomeEstado.'";';
                $stJs .= 'f.inCodMunicipioCorresp.value = "'.$rsLogradouro->getCampo ("cod_municipio").'";';
                $stJs .= 'f.inCodUFCorresp.value = "'.$rsLogradouro->getCampo ("cod_uf").'";';

                $obRCIMTrecho->setCodigoLogradouro( $rsLogradouro->getCampo ("cod_logradouro") );
                $obRCIMTrecho->listarBairroLogradouro( $rsBairro );
                $obRCIMTrecho->listarCEP( $rsCep );

                $stJs2 .= "limpaSelect(f.cmbBairroCorresp,0); \n\r";
                $stJs2 .= "limpaSelect(f.cmbCEPCorresp,0); \n\r";
                $stJs2 .= "f.cmbBairroCorresp[0] = new Option('Selecione','', 'selected');\n\r";
                $stJs2 .= "f.cmbCEPCorresp[0] = new Option('Selecione','', 'selected');\n\r";

                /* bairro ****************/
                $inContador = 1;
                while ( !$rsBairro->eof() ) {

                    $inCodBairroTMP  = $rsBairro->getCampo( "cod_bairro" );
                    $stNomeBairroTMP = $rsBairro->getCampo( "nom_bairro" );
                    if ($rsBairro->getNumLinhas()==1) {
                        $stJs2 .= "f.cmbBairroCorresp.options[$inContador] = new Option('".$stNomeBairroTMP."','".$inCodBairroTMP."','selected'); \n\r";
                        $stJs2 .= "f.inCodigoBairroCorresp.value = '".$inCodBairroTMP."'; \n\r";
                    } else {
                        $stJs2 .= "f.cmbBairroCorresp.options[$inContador] = new Option('".$stNomeBairroTMP."','".$inCodBairroTMP."'); \n\r";
                        $stJs2 .= 'f.inCodigoBairroCorresp.value = "";';
                    }
                    $inContador++;
                    $rsBairro->proximo();

                }
                if ($rsBairro->getNumLinhas()<>1) {
                    $stJs .= 'f.inCodigoBairroCorresp.value = "";';
                }

                /* cep *******************/
                $inContador = 1;
                while ( !$rsCep->eof() ) {
                    $stCep = $rsCep->getCampo( "cep" );
                    if ($rsCep->getNumLinhas()==1) {
                        $stJs2 .= "f.cmbCEPCorresp.options[$inContador] = new Option('".$stCep."','".$stCep."','selected'); \n";
                    } else {
                        $stJs2 .= "f.cmbCEPCorresp.options[$inContador] = new Option('".$stCep."','".$stCep."'); \n";
                    }
                    $inContador++;
                    $rsCep->proximo();
                }

                SistemaLegado::executaFrameOculto($stJs2);
            }
        }

        SistemaLegado::executaFrameOculto($stJs);
        break;

    case 669: // Utilizado pelo Endereço Correspondência do CGM
        include_once ( CAM_GT_CIM_NEGOCIO."RCIMTrecho.class.php" );

        if ($_REQUEST["inNumLogradouroCorresp"]) {
            $obRCIMTrecho       = new RCIMTrecho;
            $obRCIMTrecho->setCodigoLogradouro( $_REQUEST["inNumLogradouroCorresp"] );
            $obRCIMTrecho->listarBairroLogradouro( $rsBairro );
            $obRCIMTrecho->listarCEP( $rsCep );

            $stJs2 .= 'f.inCodigoBairroCorresp.value = "";';
            $stJs2 .= "limpaSelect(f.cmbBairroCorresp,0); \n\r";
            $stJs2 .= "limpaSelect(f.cmbCEPCorresp,0); \n\r";
            $stJs2 .= "f.cmbBairroCorresp[0] = new Option('Selecione','', 'selected');\n\r";
            $stJs2 .= "f.cmbCEPCorresp[0] = new Option('Selecione','', 'selected');\n\r";
            /* bairro ****************/
            $inContador = 1;
            while ( !$rsBairro->eof() ) {
                $inCodBairroTMP  = $rsBairro->getCampo( "cod_bairro" );
                $stNomeBairroTMP = $rsBairro->getCampo( "nom_bairro" );
                if ($_REQUEST["inCodigoBairroCorresp"]==$inCodBairroTMP) {
                    $stJs2 .= "parent.telaPrincipal.document.frm.inCodigoBairroCorresp.value = '".$inCodBairroTMP."'; \n\r";
                    $stJs2 .= "f.cmbBairroCorresp.options[$inContador] = new Option('".$stNomeBairroTMP."','".$inCodBairroTMP."','selected'); \n\r";
                } else {
                    $stJs2 .= "f.cmbBairroCorresp.options[$inContador] = new Option('".$stNomeBairroTMP."','".$inCodBairroTMP."'); \n\r";
                }
                $inContador++;
                $rsBairro->proximo();
            }

            /* cep *******************/
            $inContador = 1;
            while ( !$rsCep->eof() ) {
                $stCep = $rsCep->getCampo( "cep" );
                if (str_replace('-','',$_REQUEST["hdnCEPCorresp"])==$stCep) {
                    $stJs2 .= "f.cmbCEPCorresp.options[$inContador] = new Option('".$stCep."','".$stCep."','selected'); \n\r";
                } else {
                    $stJs2 .= "f.cmbCEPCorresp.options[$inContador] = new Option('".$stCep."','".$stCep."'); \n\r";
                }
                $inContador++;
                $rsCep->proximo();
            }

            SistemaLegado::executaFrameOculto($stJs2);
        }
        break;

    case 668: // Utilizado pelo Endereço do CGM
        include_once ( CAM_GT_CIM_NEGOCIO."RCIMTrecho.class.php" );

        if ($_REQUEST["inNumLogradouro"]) {
            $obRCIMTrecho       = new RCIMTrecho;
            $obRCIMTrecho->setCodigoLogradouro( $_REQUEST["inNumLogradouro"] );
            $obRCIMTrecho->listarBairroLogradouro( $rsBairro );
            $obRCIMTrecho->listarCEP( $rsCep );

            $stJs2 .= 'f.inCodigoBairro.value = "";';
            $stJs2 .= "limpaSelect(f.cmbBairro,0); \n\r";
            $stJs2 .= "limpaSelect(f.cmbCEP,0); \n\r";
            $stJs2 .= "f.cmbBairro[0] = new Option('Selecione','', 'selected');\n\r";
            $stJs2 .= "f.cmbCEP[0] = new Option('Selecione','', 'selected');\n\r";
            /* bairro ****************/
            $inContador = 1;
            while ( !$rsBairro->eof() ) {
                $inCodBairroTMP  = $rsBairro->getCampo( "cod_bairro" );
                $stNomeBairroTMP = $rsBairro->getCampo( "nom_bairro" );
                if ($_REQUEST["inCodigoBairro"]==$inCodBairroTMP) {
                    $stJs2 .= "parent.telaPrincipal.document.frm.inCodigoBairro.value = '".$inCodBairroTMP."'; \n\r";

                    $stJs2 .= "f.cmbBairro.options[$inContador] = new Option('".$stNomeBairroTMP."','".$inCodBairroTMP."','selected'); \n\r";
                } else {
                    $stJs2 .= "f.cmbBairro.options[$inContador] = new Option('".$stNomeBairroTMP."','".$inCodBairroTMP."'); \n\r";
                }
                $inContador++;
                $rsBairro->proximo();
            }

            if (count($rsBairro->getElementos()) == 1) {
                $stJs2 .= "jQuery('#cmbBairro',parent.frames[2].document).prop('selectedIndex', 1); ";
            }

            /* cep *******************/
            $inContador = 1;
            while ( !$rsCep->eof() ) {
                $stCep = $rsCep->getCampo( "cep" );
                if (str_replace('-','',$_REQUEST["hdnCEP"])==$stCep) {
                    $stJs2 .= "f.cmbCEP.options[$inContador] = new Option('".$stCep."','".$stCep."','selected'); \n\r";
                } else {
                    $stJs2 .= "f.cmbCEP.options[$inContador] = new Option('".$stCep."','".$stCep."'); \n\r";
                }
                $inContador++;
                $rsCep->proximo();
            }

            if (count($rsCep->getElementos()) == 1) {
                $stJs2 .= "jQuery('#cmbCEP',parent.frames[2].document).prop('selectedIndex', 1); ";
            }

            SistemaLegado::executaFrameOculto($stJs2);
        }
        break;

    case 666:
        include_once ( CAM_GT_CIM_NEGOCIO."RCIMTrecho.class.php" );
        $obRCIMTrecho       = new RCIMTrecho;
        $rsLogradouro       = new RecordSet;

        if ( empty( $_REQUEST["inNumLogradouro"] ) || empty( $_REQUEST["pais"] ) ) {
            $stJs .= 'd.getElementById("campoInnerLogr").innerHTML = "&nbsp;";';
            $stJs .= 'd.getElementById("stMunicipio").innerHTML = "&nbsp;";';
            $stJs .= 'd.getElementById("stEstado").innerHTML = "&nbsp;";';
            $stJs .= "limpaSelect(f.cmbBairro,0); \n\r";
            $stJs .= "limpaSelect(f.cmbCEP,0); \n\r";
            $stJs .= "f.cmbBairro[0] = new Option('Selecione','', 'selected');\n\r";
            $stJs .= "f.cmbCEP[0] = new Option('Selecione','', 'selected');\n\r";
        } else {
            $obRCIMTrecho->setCodigoLogradouro( $_REQUEST["inNumLogradouro"] ) ;
            $obRCIMTrecho->listarLogradourosTrecho( $rsLogradouro, "", $_REQUEST["pais"] );
            if ( $rsLogradouro->eof() ) {
                $stJs .= 'f.inNumLogradouro.value = "";';
                $stJs .= 'f.inNumLogradouro.focus();';
                $stJs .= 'd.getElementById("campoInnerLogr").innerHTML = "&nbsp;";';
                $stJs .= 'd.getElementById("stMunicipio").innerHTML = "&nbsp;";';
                $stJs .= 'd.getElementById("stEstado").innerHTML = "&nbsp;";';

                $stJs .= "limpaSelect(f.cmbBairro,0); \n\r";
                $stJs .= "limpaSelect(f.cmbCEP,0); \n\r";
                $stJs .= "f.cmbBairro[0] = new Option('Selecione','', 'selected');\n\r";
                $stJs .= "f.cmbCEP[0] = new Option('Selecione','', 'selected');\n\r";

                $stJs .= "alertaAviso('@Valor inválido. (".$_REQUEST["inNumLogradouro"].")', 'form','erro','".Sessao::getId()."');";
            } else {
                $stNomeLogradouro = $rsLogradouro->getCampo ("tipo_nome");
                $stJs .= 'd.getElementById("campoInnerLogr").innerHTML = "'.$stNomeLogradouro.'";';

                $stNomeMunicipio = $rsLogradouro->getCampo ("cod_municipio") . ' - ' .$rsLogradouro->getCampo ("nom_municipio");
                $stNomeEstado = $rsLogradouro->getCampo ("cod_uf") . ' - ' .$rsLogradouro->getCampo ("nom_uf");

                $stJs .= "f.stNomeLogradouro.value = '". $stNomeLogradouro."';";
                $stJs .= 'd.getElementById("stMunicipio").innerHTML = "'.$stNomeMunicipio.'";';
                $stJs .= 'd.getElementById("stEstado").innerHTML = "'.$stNomeEstado.'";';
                $stJs .= 'f.inCodMunicipio.value = "'.$rsLogradouro->getCampo ("cod_municipio").'";';
                $stJs .= 'f.inCodUF.value = "'.$rsLogradouro->getCampo ("cod_uf").'";';

                $obRCIMTrecho->setCodigoLogradouro( $rsLogradouro->getCampo ("cod_logradouro") );
                $obRCIMTrecho->listarBairroLogradouro( $rsBairro );
                $obRCIMTrecho->listarCEP( $rsCep );

                $stJs2 .= "limpaSelect(f.cmbBairro,0); \n\r";
                $stJs2 .= "limpaSelect(f.cmbCEP,0); \n\r";
                $stJs2 .= "f.cmbBairro[0] = new Option('Selecione','', 'selected');\n\r";
                $stJs2 .= "f.cmbCEP[0] = new Option('Selecione','', 'selected');\n\r";
                /* bairro ****************/
                $inContador = 1;
                while ( !$rsBairro->eof() ) {

                    $inCodBairroTMP  = $rsBairro->getCampo( "cod_bairro" );
                    $stNomeBairroTMP = $rsBairro->getCampo( "nom_bairro" );
                    if ($rsBairro->getNumLinhas()==1) {
                        $stJs2 .= "f.cmbBairro.options[$inContador] = new Option('".$stNomeBairroTMP."','".$inCodBairroTMP."','selected'); \n\r";
                        $stJs2 .= "f.inCodigoBairro.value = '".$inCodBairroTMP."'; \n\r";
                    } else {
                        $stJs2 .= "f.cmbBairro.options[$inContador] = new Option('".$stNomeBairroTMP."','".$inCodBairroTMP."'); \n\r";
                        $stJs2 .= 'f.inCodigoBairro.value = "";';
                    }
                    $inContador++;
                    $rsBairro->proximo();

                }
                if ($rsBairro->getNumLinhas()<>1) {
                    $stJs .= 'f.inCodigoBairro.value = "";';
                }
                if (count($rsBairro->getElementos()) == 1) {
                    $stJs .= "jQuery('#cmbBairro',parent.frames[2].document).prop('selectedIndex', 1); ";
                }

                /* cep *******************/
                $inContador = 1;
                while ( !$rsCep->eof() ) {
                    $stCep = $rsCep->getCampo( "cep" );
                    if ($rsCep->getNumLinhas()==1) {
                        $stJs2 .= "f.cmbCEP.options[$inContador] = new Option('".$stCep."','".$stCep."','selected'); \n";
                    } else {
                        $stJs2 .= "f.cmbCEP.options[$inContador] = new Option('".$stCep."','".$stCep."'); \n";
                    }
                    $inContador++;
                    $rsCep->proximo();
                }
                if (count($rsCep->getElementos()) == 1) {
                    $stJs2 .= "jQuery('#cmbCEP',parent.frames[2].document).prop('selectedIndex', 1); ";
                }

                SistemaLegado::executaFrameOculto($stJs2);
            }
        }

        SistemaLegado::executaFrameOculto($stJs);
        break;

    case 0: /** Monta o formulário de busca **/
        $html->formBuscaCgm('', 2);
    break;
    case 1: //O valor 1 da variável $controle está reservado para montar o formulário do CGM
        
        $numCgm = $_REQUEST['numCgm'];
        $pessoa = $_REQUEST['pessoa'];
        $cnpj = $_REQUEST['cnpj'];
        $cpf = $_REQUEST['cpf'];
        $rg = $_REQUEST['rg'];
        $paisCorresp = $_REQUEST['paisCorresp'];
        $dtValidadeCnh = $_REQUEST['dtValidadeCnh'];
        $catHabilitacao = $_REQUEST['catHabilitacao'];
        $pais = $_REQUEST['pais'];
        $stTipoAlteracao  = $_REQUEST['stTipoAlteracao'];
        $dddRes = $_REQUEST['dddRes'];
        $foneRes = $_REQUEST['foneRes'];
        $ramalRes = $_REQUEST['ramalRes'];
        $dddCom = $_REQUEST['dddCom'];
        $foneCom  = $_REQUEST['foneCom'];
        $ramalCom = $_REQUEST['ramalCom'];
        $dddCel = $_REQUEST['dddCel'];
        $foneCel = $_REQUEST['foneCel'];
        $email = $_REQUEST['email'];
        $emailAdic  = $_REQUEST['emailAdic'];
        $tipo = $_REQUEST['tipo'];
        $pessoa = $_REQUEST['pessoa'];
        $nacionalidade = $_REQUEST['nacionalidade'];
        $cod_escolaridade = $_REQUEST['cod_escolaridade'];
        $cod_pais = $_REQUEST['pais'];
        $cod_paisCorresp = $_REQUEST['paisCorresp'];
        $inscEstadual = $_REQUEST['inscEstadual'];
        $cod_orgao_registro = $_REQUEST['cmbOrgao'];
        $num_registro = $_REQUEST['inNumRegistro'];
        $dt_registro = $_REQUEST['stDataRegistro'];
        $num_registro_cvm = $_REQUEST['inNumCVM'];
        $dt_registro_cvm = $_REQUEST['stDataRegistroCVM'];
        $objeto_social = $_REQUEST['stOjetoSocial'];
        $orgaoEmissor = $_REQUEST['orgaoEmissor'];
        $inCodUFOrgaoEmissor = $_REQUEST['inCodUFOrgaoEmissor'];
        $dtEmissaoRg = $_REQUEST['dtEmissaoRg'];
        $numCnh = $_REQUEST['numCnh'];
        $nomFantasia = $_REQUEST['nomFantasia'];
        $dtNascimento = $_REQUEST['dtNascimento'];
        $chSexo = $_REQUEST['chSexo'];

        //Popular o array com os dados recebidos do form
        $arDtValidadeCnh = preg_split("/[^a-zA-Z0-9]/", $dtValidadeCnh);

        if ($paisCorresp == "xxx") {
            $paisCorresp = 0;
        }

        if ( strtolower($catHabilitacao) == "xxx" ) {
            $catHabilitacao = '0';
        }
                
        $sSQL =  "SELECT * FROM sw_nome_logradouro WHERE cod_logradouro = ".$_REQUEST["inNumLogradouro"];
        $sSQL .= " AND  timestamp = (SELECT max(timestamp) FROM sw_nome_logradouro where cod_logradouro = ".$_REQUEST["inNumLogradouro"].") ";
        $dbEmp = new dataBaseLegado;
        $dbEmp->abreBD();
        $dbEmp->abreSelecao($sSQL);
        $dbEmp->vaiPrimeiro();
        $inCodTipoLogradouro = 0;
        if ( !$dbEmp->eof() ) {
            $inCodTipoLogradouro = $dbEmp->pegaCampo("cod_tipo");
            $stNomeLogradouro = $dbEmp->pegaCampo("nom_logradouro");
        }

        $dbEmp->limpaSelecao();
        $dbEmp->fechaBD();

        $stNomeBairro = "";
        if ($_REQUEST["cmbBairro"]) {
            $sSQL = "SELECT * FROM sw_bairro WHERE cod_bairro = ".$_REQUEST["cmbBairro"];
            $dbEmp = new dataBaseLegado;
            $dbEmp->abreBD();
            $dbEmp->abreSelecao($sSQL);
            $dbEmp->vaiPrimeiro();
            if ( !$dbEmp->eof() ) {
                 $stNomeBairro = $dbEmp->pegaCampo("nom_bairro");
            }

            $dbEmp->limpaSelecao();
            $dbEmp->fechaBD();
        }

        $stNomeTipoLogradouro = "";
        if ($inCodTipoLogradouro) {
            $sSQL = "SELECT * FROM sw_tipo_logradouro WHERE cod_tipo = ".$inCodTipoLogradouro;
            $dbEmp = new dataBaseLegado;
            $dbEmp->abreBD();
            $dbEmp->abreSelecao($sSQL);
            $dbEmp->vaiPrimeiro();
            if ( !$dbEmp->eof() ) {
                 $stNomeTipoLogradouro = $dbEmp->pegaCampo("nom_tipo");
            }

            $dbEmp->limpaSelecao();
            $dbEmp->fechaBD();
        }

        $inCodTipoLogradouroCorresp = 0;
        $stNomeTipoLogradouroCorresp = "";
        $stNomeBairroCorresp = "";
        if ($_REQUEST["inNumLogradouroCorresp"]) {
            $sSQL = "SELECT * FROM sw_nome_logradouro WHERE cod_logradouro = ".$_REQUEST["inNumLogradouroCorresp"];
            $sSQL .= " AND  timestamp = (SELECT max(timestamp) FROM sw_nome_logradouro where cod_logradouro = ".$_REQUEST["inNumLogradouroCorresp"].") ";
            $dbEmp = new dataBaseLegado;
            $dbEmp->abreBD();
            $dbEmp->abreSelecao($sSQL);
            $dbEmp->vaiPrimeiro();
            if ( !$dbEmp->eof() ) {
                $inCodTipoLogradouroCorresp = $dbEmp->pegaCampo("cod_tipo");
                $stNomeLogradouroCorresp = $dbEmp->pegaCampo("nom_logradouro");
            }

            $dbEmp->limpaSelecao();
            $dbEmp->fechaBD();

            if ($inCodTipoLogradouroCorresp) {
                $sSQL = "SELECT * FROM sw_tipo_logradouro WHERE cod_tipo = ".$inCodTipoLogradouroCorresp;
                $dbEmp = new dataBaseLegado;
                $dbEmp->abreBD();
                $dbEmp->abreSelecao($sSQL);
                $dbEmp->vaiPrimeiro();
                if ( !$dbEmp->eof() ) {
                    $stNomeTipoLogradouroCorresp = $dbEmp->pegaCampo("nom_tipo");
                }

                $dbEmp->limpaSelecao();
                $dbEmp->fechaBD();
            }

            if ($_REQUEST["cmbBairroCorresp"]) {
                $sSQL = "SELECT * FROM sw_bairro WHERE cod_bairro = ".$_REQUEST["cmbBairroCorresp"];
                $dbEmp = new dataBaseLegado;
                $dbEmp->abreBD();
                $dbEmp->abreSelecao($sSQL);
                $dbEmp->vaiPrimeiro();
                if ( !$dbEmp->eof() ) {
                    $stNomeBairroCorresp = $dbEmp->pegaCampo("nom_bairro");
                }

                $dbEmp->limpaSelecao();
                $dbEmp->fechaBD();
            }
        }

        //-------------------------
        $sSQL = "SELECT * FROM sw_cgm_logradouro WHERE numcgm = ".$numCgm;
        $dbEmp = new dataBaseLegado;
        $dbEmp->abreBD();
        $dbEmp->abreSelecao($sSQL);
        $dbEmp->vaiPrimeiro();
        if ( $dbEmp->eof() )
            $boCgmLogradouro = false;
        else
            $boCgmLogradouro = true;

        $dbEmp->limpaSelecao();
        $dbEmp->fechaBD();
        //----------------------------
        //-------------------------
        $sSQL = "SELECT * FROM sw_cgm_logradouro_correspondencia WHERE numcgm = ".$numCgm;
        $dbEmp = new dataBaseLegado;
        $dbEmp->abreBD();
        $dbEmp->abreSelecao($sSQL);
        $dbEmp->vaiPrimeiro();
        if ( $dbEmp->eof() )
            $boCgmLogradouroCorresp = false;
        else
            $boCgmLogradouroCorresp = true;

        $dbEmp->limpaSelecao();
        $dbEmp->fechaBD();
        //----------------------------

        $dadosCgm = array(
            stNomeBairro=>$stNomeBairro,
            stNomeBairroCorresp=>$stNomeBairroCorresp,
            stNomeLogradouro=>$stNomeLogradouro,
            stNomeTipoLogradouro=>$stNomeTipoLogradouro,
            stNomeLogradouroCorresp=>$stNomeLogradouroCorresp,
            stNomeTipoLogradouroCorresp=>$stNomeTipoLogradouroCorresp,
            boCgmLogradouroCorresp=>$boCgmLogradouroCorresp,
            boCgmLogradouro=>$boCgmLogradouro,
            numCgm=>$numCgm,
            stTipoAlteracao=>$stTipoAlteracao,
            codUf=>$_REQUEST["inCodUF"],
            nomUf=>$_REQUEST["nomUf"],
            codpais=>$cod_pais,
            codpaisCorresp=>$cod_paisCorresp,
            codMunicipioCorresp=>$_REQUEST["inCodMunicipioCorresp"]?$_REQUEST["inCodMunicipioCorresp"]:0,
            codUfCorresp=>$_REQUEST["inCodUFCorresp"]?$_REQUEST["inCodUFCorresp"]:0,
            nomUfCorresp=>$_REQUEST["nomUfCorresp"],
            nomCgm=>trim($_REQUEST['nomCgm']),
            logradouro=>$_REQUEST["inNumLogradouro"],
            numero=>$_REQUEST["inNumero"],
            complemento=>$_REQUEST["stComplemento"],
            pais=>$pais,
            estado=>$_REQUEST["inCodUF"],
            municipio=>$_REQUEST["inCodMunicipio"],
            codMunicipio=>$_REQUEST["inCodMunicipio"],
            nomMunicipio=>$_REQUEST["nomMunicipio"],
            bairro=>$_REQUEST["cmbBairro"],
            nacionalidade=>$nacionalidade,
            cod_escolaridade=>$cod_escolaridade,
            cep=>preg_replace('/[^a-zA-Z0-9]/','', $_REQUEST["cmbCEP"] ),
            tipoLogradouro=>$inCodTipoLogradouro,
            tipoLogradouroCorresp=>$inCodTipoLogradouroCorresp,
            logradouroCorresp=>$_REQUEST["inNumLogradouroCorresp"]?$_REQUEST["inNumLogradouroCorresp"]:0,
            numeroCorresp=>$_REQUEST["inNumeroCorresp"],
            complementoCorresp=>$_REQUEST["stComplementoCorresp"],
            paisCorresp=>$paisCorresp,
            estadoCorresp=>$_REQUEST["inCodUFCorresp"]?$_REQUEST["inCodUFCorresp"]:0,
            municipioCorresp=>$_REQUEST["inCodMunicipioCorresp"]?$_REQUEST["inCodMunicipioCorresp"]:0,
            nomMunicipioCorresp=>$_REQUEST["nomMunicipioCorresp"],
            bairroCorresp=>$_REQUEST["cmbBairroCorresp"],
            cepCorresp=>preg_replace('/[^a-zA-Z0-9]/','', $_REQUEST["cmbCEPCorresp"] ),
            foneRes=>$dddRes.$foneRes,
            ramalRes=>$ramalRes,
            foneCom=>$dddCom.$foneCom,
            ramalCom=>$ramalCom,
            foneCel=>$dddCel.$foneCel,
            email=>$email,
            emailAdic=>$emailAdic,
            codResp=>Sessao::read('numCgm'),
            pessoa=>$_REQUEST['pessoa'],
            cnpj=>preg_replace('/[^a-zA-Z0-9]/','', $_REQUEST['cnpj'] ),
            nomFantasia=>$_REQUEST['nomFantasia'],
            inscEst=>$_REQUEST['inscEstadual'],
            cod_orgao_registro=>$cod_orgao_registro,
            num_registro=> $num_registro,
            dt_registro=> $dt_registro,
            num_registro_cvm=> $num_registro_cvm,
            dt_registro_cvm=> $dt_registro_cvm,
            objeto_social=> $objeto_social,
            cpf=>preg_replace('/[^a-zA-Z0-9]/','', $_REQUEST['cpf'] ),
            orgaoEmissor=>$_REQUEST['orgaoEmissor'],
            inCodUFOrgaoEmissor=>$_REQUEST['inCodUFOrgaoEmissor'],
            dtEmissaoRg=>$_REQUEST['dtEmissaoRg'],
            numCnh=>$_REQUEST['numCnh'],
            catHabilitacao=>$_REQUEST['catHabilitacao'],
            dtValidadeCnh=>$_REQUEST['dtValidadeCnh'],
            nacionalidade=>$_REQUEST['nacionalidade'],
            cod_escolaridade=>$_REQUEST['cod_escolaridade'],
            dtNascimento=>$_REQUEST['dtNascimento'],
            chSexo=>$_REQUEST['chSexo'],
            rg=>$_REQUEST['rg'],
            site=>$_REQUEST['stSite'],
            );

        $cpfigual = $cpf;
        $cnpjigual = $cnpj;

        $objCgm = new cgmLegado;
        if ($_REQUEST['atributo']) {
            $objCgm->setAtributo( $_REQUEST['atributo'] );
        }
        
        //a acao que estava na sessao era perdida, validacao da acao para a insercao da auditoria
        !Sessao::read('acao') ? Sessao::write('acao',39) : '';
        
        if ($pessoa == "fisica") {

            if (comparaValor("cpf", preg_replace('/[^a-zA-Z0-9]/','', $cpf ), "sw_cgm_pessoa_fisica", "and numcgm <> $dadosCgm[numCgm]")) {
                if ($objCgm->alteraCgm($dadosCgm)) {
                    //Insere auditoria
                    $audicao = new auditoriaLegada;
                    $audicao->setaAuditoria(Sessao::read('numCgm'), Sessao::read('acao'), $dadosCgm['numCgm']);
                    $audicao->insereAuditoria();
                    alertaAviso($PHP_SELF."?controle=2&pagina=".$pagina."&volta=true","CGM $dadosCgm[numCgm]","alterar","aviso");
                } else {
                    $stMensagem = $objCgm->stErro;
                    Sessao::write('dadosCgm', $dadosCgm);
                    alertaAviso($_SERVER['PHP_SELF']."?controle=5&numCgm=".$dadosCgm['numCgm']."&pessoa=".$pessoa,"CGM $dadosCgm[numCgm] $stMensagem","n_alterar","erro");
                }
            } else {
                echo '
                    <script type="text/javascript">
                    alertaAviso("O cpf '.$cpfigual.' já existe","unica","erro","'.Sessao::getId().'");
                    </script>';
                    $html->formCgm($dadosCgm,$_SERVER['PHP_SELF'],0);
            }
        } elseif ($pessoa == "juridica") {

            if (comparaValor("cnpj", preg_replace('/[^a-zA-Z0-9]/','', $cnpj ), "sw_cgm_pessoa_juridica", "and numcgm <> $dadosCgm[numCgm]")) {
                if ($objCgm->alteraCgm($dadosCgm)) {
                    //Insere auditoria
                    $audicao = new auditoriaLegada;
                    $audicao->setaAuditoria(Sessao::read('numCgm'), Sessao::read('acao'), $dadosCgm['numCgm']);
                    $audicao->insereAuditoria();
                    alertaAviso($PHP_SELF."?controle=2&pagina=".$pagina."&volta=true","CGM ".$dadosCgm['numCgm']."","alterar","aviso");
                } else {
                    $stMensagem = $objCgm->stErro;
                    Sessao::write('dadosCgm', $dadosCgm);
                    alertaAviso($_SERVER['PHP_SELF']."?controle=5&numCgm=".$dadosCgm['numCgm']."&pessoa=".$pessoa,"CGM $dadosCgm[numCgm] $stMensagem","n_alterar","erro");
                }
            } else {
                echo '
                    <script type="text/javascript">
                    alertaAviso("O cnpj '.$cnpjigual.' já existe","unica","erro","'.Sessao::getId().'");
                    </script>';
                    $html->formCgm($dadosCgm,$_SERVER['PHP_SELF'],0);
            }
        } elseif ($pessoa == "outros") {

            if ($cpf == "") {
                $cpfCnpj = $cnpj;
            } else {
                $cpfCnpj = $cpf;
            }

            if (comparaValor("cpf", preg_replace('/[^a-zA-Z0-9]/','', $cpfCnpj ), "sw_cgm_pessoa_fisica", "and numcgm <> ".$dadosCgm['numCgm']."")) {
                if ($objCgm->alteraCgm($dadosCgm)) {
                    //Insere auditoria
                    $audicao = new auditoriaLegada;
                    $audicao->setaAuditoria(Sessao::read('numCgm'), Sessao::read('acao'), $dadosCgm['numCgm']);
                    $audicao->insereAuditoria();
                    alertaAviso($_SERVER['PHP_SELF']."?pessoa=".$pessoa."&tipo=".$tipo."&controle=2&pagina=".$pagina."&volta=true","CGM ".$dadosCgm['numCgm'],"alterar","aviso");
                } else {
                    $stMensagem = $objCgm->stErro;
                    Sessao::write('dadosCgm', $dadosCgm);
                    alertaAviso($_SERVER['PHP_SELF']."?controle=6&numCgm=".$dadosCgm['numCgm']."&pessoa=".$pessoa."&tipo=".$tipo,"CGM $dadosCgm[numCgm] $stMensagem","n_alterar","erro");
                }
            } else {
                Sessao::write('dadosCgm', $dadosCgm);
                $stMensagem = "O CPF $cpf já existe.";
                alertaAviso($_SERVER['PHP_SELF']."?controle=6&numCgm=".$dadosCgm['numCgm']."&pessoa=".$pessoa."&tipo=".$tipo,$stMensagem,"n_alterar","erro");
            }
        }
    break;

    case 2: /** Exibe uma lista com o resultado da busca **/
        //** Monta um vetor com os dados recebidos do formulário de busca **/
        if ($_GET['volta'] == 'true' or $_GET['paginando'] == 'true') {
            $dadosBusca = Sessao::read('dadosBusca');
        } else {
            $dadosBusca = array(
                numCgm=>$_REQUEST["numCgm"],
                nomCgm=>str_replace('\'','\\\\\\\'\'',$_REQUEST["nomCgm"]),
                cnpj=>preg_replace('/[^a-zA-Z0-9]/','', $_REQUEST["cnpj"] ),
                cpf=>preg_replace('/[^a-zA-Z0-9]/','', $_REQUEST["cpf"] ),
                rg=>$_REQUEST["rg"],
                tipoBusca=>$_REQUEST["tipoBusca"] );
            Sessao::write('dadosBusca', $dadosBusca);
        }
        $objCgm = new cgmLegado;
        $html = new interfaceCgm;
        $html->exibeBusca($objCgm->montaPesquisaCgm($dadosBusca),'alterar', 'cgm', $controle);
        /** Envia o vetor com a busca e recebe uma matriz com o resultado **/
    break;
    case 3: /** Monta o formulário com os dados do CGM escolhido **/
        $html = new interfaceCgm;
        $objCgm = new cgmLegado;
        $arDadosCgm = $objCgm->pegaDadosCgm($_REQUEST["numCgm"]);

        if ($arDadosCgm["pessoa"] == "interno") {
            Sessao::write('dadosCgm', $arDadosCgm);

            $stCombo = $html->comboTipoCgm("tipo","","onChange='document.frm.controle.value=6;document.frm.submit();'");
echo <<<HEREDOC
        <form name='frm' action='$PHP_SELF?Sessao::getId()' method='post'>
            <input type="hidden" name="controle" value="">
            <table width='100%'>
                <tr>
                    <td class=alt_dados colspan=2>Tipo de Cadastro</td>
                </tr>
                <tr>
                     <td class="label" width="30%" title="Tipo de cadastro: Pessoa Física ou Jurídica">*Tipo de cadastro</td>
                     <td class="field" width="70%">$stCombo</td>
                </tr>
            </table>
        </form>
HEREDOC;
        } else {
            $html->formCgm($arDadosCgm,$PHP_SELF,0);
        }
    break;
    case 4:
            if ($campoMunicipio == "municipio") {
                $uf = $estado;
                $nomeuf = "estado";
                $m = "municipio";
            } else {
                $uf = $estadoCorresp;
                $nomeuf = "estadoCorresp";
                $m = "municipioCorresp";
            }
            if ($uf != 'xxx') {
                $sSQL = "SELECT * FROM sw_municipio WHERE cod_uf = ".$uf." ORDER by nom_municipio";
                $dbEmp = new dataBaseLegado;
                $dbEmp->abreBD();
                $dbEmp->abreSelecao($sSQL);
                $dbEmp->vaiPrimeiro();
                $comboMunicipio = "";
                $iCont = 1;
                $js .= "limpaSelect(f.".$campoMunicipio.",1); \n";

                while (!$dbEmp->eof()) {
                    $codg_municipio  = trim($dbEmp->pegaCampo("cod_municipio"));
                    $nomg_municipio  = trim($dbEmp->pegaCampo("nom_municipio"));
                    $dbEmp->vaiProximo();
                    $js .= "f.".$campoMunicipio.".options[".$iCont++."] = new Option('".addslashes($nomg_municipio)."','".$codg_municipio."');\n";
                }
                if ($iCont > 1) {
                   $js .= "f.".$campoMunicipio.".disabled = false;\n";
                   $js .= "f.".$campoMunicipio.".focus();\n";
                }
                $dbEmp->limpaSelecao();
                $dbEmp->fechaBD();
            } else {
                $js .= "limpaSelect(f.".$m.",1); \n";
            }
            executaFrameOculto($js);

    break;
    case 5: /** Monta o formulário com os dados do CGM escolhido **/
        $html = new interfaceCgm;
        $html->formCgm(Sessao::read('dadosCgm'),$PHP_SELF,0);
        Sessao::remove('dadosCgm');
    break;
    case 6:
         $arDadosCgm = Sessao::read('dadosCgm');
         $arDadosCgm['pessoa'] = 'outros';
         $arDadosCgm['tipo'] = $_REQUEST['tipo'];
         Sessao::write('dadosCgm', array());
         $html->formCgm( $arDadosCgm,$PHP_SELF,0);
    break;
    case 2001:
        $stJs .= 'f.inNumLogradouroCorresp.value = "";';
        $stJs .= 'f.inNumLogradouroCorresp.focus();';
        $stJs .= 'd.getElementById("campoInnerLogrCorresp").innerHTML = "&nbsp;";';
        $stJs .= 'd.getElementById("stMunicipioCorresp").innerHTML = "&nbsp;";';
        $stJs .= 'd.getElementById("stEstadoCorresp").innerHTML = "&nbsp;";';
        $stJs .= 'f.inCodigoBairroCorresp.value = "";';
        $stJs .= 'f.inNumeroCorresp.value = "";';
        $stJs .= "limpaSelect(f.cmbBairroCorresp,0); \n\r";
        $stJs .= "limpaSelect(f.cmbCEPCorresp,0); \n\r";
        $stJs .= "f.cmbBairroCorresp[0] = new Option('Selecione','', 'selected');\n\r";
        $stJs .= "f.cmbCEPCorresp[0] = new Option('Selecione','', 'selected');\n\r";

        SistemaLegado::executaFrameOculto($stJs);
        break;
    case 2000:
        $stJs .= 'f.inNumLogradouro.value = "";';
        $stJs .= 'f.inNumLogradouro.focus();';
        $stJs .= 'f.inCodigoBairro.value = "";';
        $stJs .= 'd.getElementById("campoInnerLogr").innerHTML = "&nbsp;";';
        $stJs .= 'd.getElementById("stMunicipio").innerHTML = "&nbsp;";';
        $stJs .= 'd.getElementById("stEstado").innerHTML = "&nbsp;";';
        $stJs .= 'f.inNumero.value = "";';
        $stJs .= "limpaSelect(f.cmbBairro,0); \n\r";
        $stJs .= "limpaSelect(f.cmbCEP,0); \n\r";
        $stJs .= "f.cmbBairro[0] = new Option('Selecione','', 'selected');\n\r";
        $stJs .= "f.cmbCEP[0] = new Option('Selecione','', 'selected');\n\r";

        SistemaLegado::executaFrameOculto($stJs);

    break;
    case 4000:
            $nome = trim($_POST["nomCgm"]);
            $js.= "nome = '".$nome."';";
            $js.= "
            function validaLetraNome(letra)
            {
                if ((letra>90 && letra<97) || (letra>122 && letra<192) || (letra>196 && letra<199) || (letra>207 && letra<210) || (letra>214 && letra<217) || (letra>221 && letra<224) || (letra>228 && letra<231) || (letra>246 && letra<249) || letra>253 || letra<65) {
                    return true;
                } else {
                    return false;
                }
            }

            if (nome.substr(1,1)==' ' || nome.substr(1,1)=='') {
                mensagem = 'Primeiro nome deve conter no mínimo duas letras!';
                alertaAviso(mensagem,'form','erro','" . Sessao::getId() . "');
                parent.telaPrincipal.document.frm.nomCgm.focus();
            } else {
                palavras = nome.split(' ');
                erroNome = false;
                for (i = 0 ; i < palavras.length ; i++) {
                    if (validaLetraNome(palavras[i].charCodeAt(palavras[i].length-1)) && palavras[i].charCodeAt(palavras[i].length-1)!=46) {
                        erroNome = palavras[i];
                        break;
                    }
                }
                if (erroNome) {
                    mensagem = 'Caracter inválido no fim do nome '+erroNome+'!';
                    alertaAviso(mensagem,'form','erro','" . Sessao::getId() . "');
                    parent.telaPrincipal.document.frm.nomCgm.focus();
                } else {
//                  if ((palavras[i-1].charCodeAt(palavras[i-1].length-1)<97 && palavras[i-1].charCodeAt(palavras[i-1].length-1)>90) || palavras[i-1].charCodeAt(palavras[i-1].length-1)>123|| palavras[i-1].charCodeAt(palavras[i-1].length-1)<65) {
                    if (validaLetraNome(palavras[i-1].charCodeAt(palavras[i-1].length-1))) {
                        mensagem = 'Último nome não pode ser abreviado!';
                        alertaAviso(mensagem,'form','erro','" . Sessao::getId() . "');
                        parent.telaPrincipal.document.frm.nomCgm.focus();
                    }
                }
            }";

            executaFrameOculto($js);
    break;
}//Fim switch

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
