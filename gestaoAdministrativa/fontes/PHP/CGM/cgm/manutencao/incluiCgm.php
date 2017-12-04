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
    * Arquivo de instância para manutenção de CGM
    * Data de Criação: 27/02/2003

    * @author Analista:
    * @author Desenvolvedor: Ricardo Lopes de Alencar

    * @package URBEM
    * @subpackage

    * Casos de uso: uc-01.02.92, uc-01.02.93

    $Id: incluiCgm.php 63238 2015-08-06 19:18:45Z lisiane $

*/

include '../../../framework/include/cabecalho.inc.php'; //Insere o início da página html
include CAM_FRAMEWORK."legado/funcoesLegado.lib.php";
include CAM_FRAMEWORK."legado/cgmLegado.class.php"; //Insere a classe que manipula os dados do CGM
include CAM_FRAMEWORK."legado/auditoriaLegada.class.php"; //Inclui classe para inserir auditoria
include 'interfaceCgm.class.php'; //Insere a classe que constroi a interface html para CGM

$objCgm = new cgmLegado;
$html = new interfaceCgm;

setAjuda('UC-01.02.92');

$stacaoTMP = Sessao::read('acao');
$stmoduloTMP = Sessao::read('modulo');
if ($stacaoTMP != 783) {
    Sessao::write( 'acaoTMP', $stacaoTMP );
    Sessao::write( 'moduloTMP', $stmoduloTMP );
} else {
    $stacaoTMP = Sessao::read('acaoTMP');
    $stmoduloTMP = Sessao::read('moduloTMP');
    Sessao::write( 'acao', $stacaoTMP );
    Sessao::write( 'modulo', $stmoduloTMP );
}

$controle = $request->get('controle');
$pessoa = $request->get('pessoa');
$tipo = $request->get('tipo');

if (!isset($controle)) {
    $controle = 0;
}
$stAcao = "inclui";

switch ($controle) {
    case 600:
        /// valida se o cpf digitado pelo usuário  já existe no banco
        if ($_REQUEST['cpf']) {
            $stCPF = $_REQUEST['cpf'];
            if ($stCPF != '') {
                include_once ( CAM_GA_CGM_MAPEAMENTO."TCGMPessoaFisica.class.php" );
                $rsCPF = new RecordSet;
                $obTCGMPessoaFisica = new TCGMPessoaFisica;
                $obTCGMPessoaFisica->setCampoCod('cpf');
                $stCPF = str_replace( '.', '', $stCPF );
                $stCPF = str_replace( '-', '', $stCPF );
                $obTCGMPessoaFisica->setDado( 'cpf', $stCPF );
                $obTCGMPessoaFisica->recuperaPorChave( $rsCPF );
                if ( $rsCPF->getNumLinhas() > 0 ) {
                    $stJs = "alertaAviso('@O CPF digitado já foi cadastrado. (".$_REQUEST["cpf"].")', 'form','erro','".Sessao::getId()."');";
                    $stJs .= 'f.cpf.value = "";';
                    SistemaLegado::executaFrameOculto($stJs);
                }
            }
       } elseif ($_REQUEST['cnpj']) {
            $stCNPJ = $_REQUEST['cnpj'];
            if ($stCNPJ != '') {
                include_once ( CAM_GA_CGM_MAPEAMENTO."TCGMPessoaJuridica.class.php" );
                $rsCNPJ = new RecordSet;
                $obTCGMPessoaFisica = new TCGMPessoaJuridica;
                $obTCGMPessoaFisica->setCampoCod('cnpj');
                $stCNPJ = str_replace( '.', '', $stCNPJ );
                $stCNPJ = str_replace( '-', '', $stCNPJ );
                $stCNPJ = str_replace( '/', '', $stCNPJ );
                $obTCGMPessoaFisica->setDado( 'cnpj', $stCNPJ );
                $obTCGMPessoaFisica->recuperaPorChave( $rsCNPJ );
                if ( $rsCNPJ->getNumLinhas() > 0 ) {
                    $stJs = "alertaAviso('@O CNPJ digitado já foi cadastrado. (".$_REQUEST["cnpj"].")', 'form','erro','".Sessao::getId()."');";
                    $stJs .= 'f.cnpj.value = "";';
                    SistemaLegado::executaFrameOculto($stJs);
                }
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
            $stJs .= 'f.inNumLogradouroCorresp.value = "";';
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

    case 669:
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

    case 668:
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
            $stJs .= 'f.inNumLogradouro.value = "";';
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

    case 0:
        //Seleciona o tipo de cadastro
        if ($pessoa != "outros") {
        ?>
        <form name='frm2' action='<?=$_SERVER['PHP_SELF'];?>?<?=Sessao::getId();?>' method='post'>
        <table width='100%'>
            <tr>
                <td class=alt_dados colspan=2>Tipo de Cadastro</td>
            </tr>
            <tr>
                <td class="label" width="30%" title="Selecione o tipo de cadastro.">*Tipo de cadastro</td>
                <td class="field" width="70%">
        <?php
        if (!isset($pessoa)) {
            echo $html->comboTipoCgm("pessoa",$pessoa,"onChange='document.frm2.submit();'");
        } else {
            echo $html->comboTipoCgm("pessoa",$pessoa,"onChange='document.frm.pessoa.value=document.frm2.pessoa.value;document.frm.submit();'");
        }
        ?>
                </td>
            </tr>
        </table>
        </form>
        <?php
            if (isset($pessoa)) {
                $dados = $_POST;
                $dados['pessoa'] = $pessoa;
                $html->formCgm($dados,$_SERVER['PHP_SELF'],0);
            } else {
?>
        <script type='text/javascript'>
        <!--
            document.frm2.pessoa.focus();
        //-->
        </script>
<?php
            }
        } elseif ($pessoa == 'outros') {
        ?>
            <form name='frm2' action='<?=$_SERVER['PHP_SELF'];?>?<?=Sessao::getId();?>' method='post'>
            <input type='hidden' name='pessoa' value='<?=$pessoa;?>'>
            <table width='100%'>
                <tr>
                    <td class=alt_dados colspan=2>Tipo de Cadastro</td>
                </tr>
                <tr>
                    <td class="label" width="30%" title="Tipo de cadastro: Pessoa Física ou Jurídica">*Tipo de cadastro</td>
                    <td class="field" width="70%">
                    <?php
                    if (!isset($_REQUEST["tipo"])) {
                        echo $html->comboTipoCgm("tipo",$request->get('tipo'),"onChange='document.frm2.submit();'");
                    } else {
                        echo $html->comboTipoCgm("tipo",$request->get('tipo'),"onChange='document.frm.tipo.value=document.frm2.tipo.value;document.frm.submit();'");
                    }
                    ?>
                    </td>
                </tr>
            </table>
            </form>
            <?php
                if (isset($_REQUEST["tipo"])) {
                    $dados = $_POST;
                    $dados['pessoa'] = $pessoa;
                    $dados['tipo'] = $tipo;
                    $html->formCgm($dados,$_SERVER['PHP_SELF'],0);
                } else {
    ?>
                    <script>
                    <!--
                        document.frm2.tipo.focus();
                    //-->
                    </script>
    <?php
                }
            }
    break;
    case 1:
        $arDadosAux = Sessao::read('dadosCgm');

        $codpaisCorresp = $_REQUEST['codpaisCorresp'];
        $paisCorresp = $_REQUEST['paisCorresp'];
        $dtValidadeCnh = $_REQUEST['dtValidadeCnh'];
        $catHabilitacao = (!empty($_REQUEST['catHabilitacao']) ? $_REQUEST['catHabilitacao'] : $arDadosAux['catHabilitacao']);
        $cpf = $_REQUEST['cpf'];
        $cnpj = $_REQUEST['cnpj'];
        $rg = $_REQUEST['rg'];

        //Popular o array com os dados recebidos do form
        if($codpaisCorresp == "xxx")
            $codpaisCorresp = 0;

        $arDtValidadeCnh = preg_split("/[^a-zA-Z0-9]/", $dtValidadeCnh);

        if ($paisCorresp == "xxx") {
            $paisCorresp = 0;
        }

        if ( strtolower($catHabilitacao) == "xxx" ) {
            $catHabilitacao = '';
        } else
            $catHabilitacao = $catHabilitacao;

        $sSQL = "SELECT * FROM sw_nome_logradouro WHERE cod_logradouro = ".$_REQUEST["inNumLogradouro"];
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

        $inCodTipoLogradouroCorresp = 0;
        $stNomeTipoLogradouroCorresp = "";
        $stNomeBairroCorresp = "";
        if ($_REQUEST["inNumLogradouroCorresp"]) {
            $sSQL = "SELECT * FROM sw_nome_logradouro WHERE cod_logradouro = ".$_REQUEST["inNumLogradouroCorresp"];
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

        include_once ( CAM_GT_CIM_NEGOCIO."RCIMTrecho.class.php" );

        $obRCIMTrecho = new RCIMTrecho;
        $obRCIMTrecho->setCodigoLogradouro( $_REQUEST["inNumLogradouro"] ) ;
        $obRCIMTrecho->listarLogradourosTrecho( $rsLogradouro, "", $_REQUEST["pais"] );
        $obRCIMTrecho->setCodigoLogradouro( $rsLogradouro->getCampo ("cod_logradouro") );
        $obRCIMTrecho->listarBairroLogradouro( $rsBairro );

        $rsBairroCorresp = new RecordSet;
        if ($_REQUEST["inNumLogradouroCorresp"]) {
            $obRCIMTrecho->setCodigoLogradouro( $_REQUEST["inNumLogradouroCorresp"] ) ;
            if ($_REQUEST["paisCorresp"]) {
                $obRCIMTrecho->listarLogradourosTrecho( $rsLogradouro, "", $_REQUEST["paisCorresp"] );
                $obRCIMTrecho->setCodigoLogradouro( $rsLogradouro->getCampo ("cod_logradouro") );
                $obRCIMTrecho->listarBairroLogradouro( $rsBairroCorresp );
            }
        }

        //$obRCIMTrecho->listarCEP( $rsCep );

        $dadosCgm = array(
            stNomeBairro=>$rsBairro->getCampo("nom_bairro"),
            stNomeBairroCorresp=>$rsBairroCorresp->getCampo("nom_bairro"),
            stNomeLogradouro=>$_REQUEST['stNomeLogradouro'],
            stNomeTipoLogradouro=>$_REQUEST['stNomeTipoLogradouro'],
            stNomeLogradouroCorresp=>$_REQUEST['stNomeLogradouroCorresp'],
            stNomeTipoLogradouroCorresp=>$_REQUEST['stNomeTipoLogradouroCorresp'],
            tipoLogradouro=>$_REQUEST['inCodTipoLogradouro'],
            tipoLogradouroCorresp=>$_REQUEST['inCodTipoLogradouroCorresp'],
            numCgm=>pegaID('numcgm',"sw_cgm"),
            codMunicipio=>$_REQUEST["inCodMunicipio"],
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
            pais=>$_REQUEST['pais'],
            estado=>$_REQUEST["inCodUF"],
            municipio=>$_REQUEST["inCodMunicipio"],
            codMunicipio=>$_REQUEST["inCodMunicipio"],
            nomMunicipio=>$_REQUEST["nomMunicipio"],
            bairro=>$_REQUEST["cmbBairro"],
            nacionalidade=>$nacionalidade,
            cod_escolaridade=>$cod_escolaridade,
            cep=>preg_replace("/[^a-zA-Z0-9]/","", $_REQUEST["cmbCEP"] ),
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
            cepCorresp=>preg_replace("/[^a-zA-Z0-9]/","", $_REQUEST["cmbCEPCorresp"] ),
            foneRes=>$_REQUEST['dddRes'].$_REQUEST['foneRes'],
            ramalRes=>$_REQUEST['ramalRes'],
            foneCom=>$_REQUEST['dddCom'].$_REQUEST['foneCom'],
            ramalCom=>$_REQUEST['ramalCom'],
            foneCel=>$_REQUEST['dddCel'].$_REQUEST['foneCel'],
            email=>$_REQUEST['email'],
            emailAdic=>$_REQUEST['emailAdic'],
            codResp=>Sessao::read('numCgm'),
            pessoa=>$_REQUEST['pessoa'],
            cnpj=>preg_replace("/[^a-zA-Z0-9]/","", $_REQUEST["cnpj"] ),
            nomFantasia=>$_REQUEST['nomFantasia'],
            inscEst=>$_REQUEST['inscEstadual'],
            cod_orgao_registro=>$_REQUEST['cmbOrgao'],
            num_registro=>$_REQUEST['inNumRegistro'],
            num_registro_cvm=>$_REQUEST['inNumCVM'],
            dt_registro=>$_REQUEST['stDataRegistro'],
            dt_registro_cvm=>$_REQUEST['stDataRegistroCVM'],
            objeto_social=>$_REQUEST['stOjetoSocial'],
            cpf=>preg_replace("/[^a-zA-Z0-9]/","", $_REQUEST["cpf"] ),
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
            site=>$_REQUEST['stSite']
            );
        
        if ($_REQUEST['atributo']) {
            $objCgm->setAtributo( $_REQUEST['atributo'] );
        }

        $cpfigual = preg_replace("/[^a-zA-Z0-9]/","", $cpf );
        $cnpjigual = preg_replace("/[^a-zA-Z0-9]/","", $cnpj );

        if ($pessoa == "fisica") {
            if (comparaValor("cpf", $cpfigual, "sw_cgm_pessoa_fisica")) {

                if ($objCgm->incluiCgm($dadosCgm)) {
                    //Insere auditoria
                    $audicao = new auditoriaLegada;
                    $audicao->setaAuditoria(Sessao::read('numCgm'), Sessao::read('acao'), $dadosCgm[numCgm]);
                    $audicao->insereAuditoria();
                    alertaAviso($PHP_SELF."?controle=0&pessoa=".$pessoa,"CGM ".$dadosCgm[numCgm],"incluir","aviso");
                } else {
                    $dadosCgm['numCgm'] = "";
                    $stMensagem = $objCgm->stErro;
                    Sessao::write('dadosCgm', $dadosCgm);
                    alertaAviso($_SERVER['PHP_SELF']."?controle=5&pessoa=".$pessoa,"CGM $stMensagem","n_incluir","erro");
                }
            } else {
                //echo '
                //    <script type="text/javascript">
                //    alertaAviso("O CPF '.$cpf.' já existe.","unica","erro","'.Sessao::getId().'");
                //    </script>';
                //$html->formCgm($dadosCgm,$PHP_SELF,0);

                $dadosCgm['numCgm']="";
                Sessao::write('dadosCgm', $dadosCgm);
                $stMensagem = "O CPF pf $cpf já existe!";
                //alertaAviso($_SERVER['PHP_SELF']."?controle=5&numCgm=".$dadosCgm['numCgm']."&pessoa=".$pessoa."&tipo=".$tipo,$stMensagem,"n_incluir","erro");
                alertaAviso($_SERVER['PHP_SELF']."?controle=5&pessoa=".$pessoa."&tipo=".$tipo,$stMensagem,"n_incluir","aviso");
            }
        } elseif ($pessoa == "juridica") {
            if (comparaValor("cnpj", $cnpjigual, "sw_cgm_pessoa_juridica")) {
                if ($objCgm->incluiCgm($dadosCgm)) {
                    //Insere auditoria
                    $audicao = new auditoriaLegada;
                    $audicao->setaAuditoria(Sessao::read('numCgm'), Sessao::read('acao'), $dadosCgm[numCgm]);
                    $audicao->insereAuditoria();
                    alertaAviso($_SERVER['PHP_SELF']."?controle=0&pessoa=".$pessoa,"CGM ".$dadosCgm[numCgm],"incluir","aviso");
                } else {
                    $stMensagem = $objCgm->stErro;
                    //sessao->transf2 = $dadosCgm;
                    Sessao::write('dadosCgm', $dadosCgm);
                    alertaAviso($_SERVER['PHP_SELF']."?controle=5&pessoa=".$pessoa,"CGM $stMensagem","n_incluir","erro");
                }
            } else {
                echo '
                    <script type="text/javascript">
                    alertaAviso("O CNPJ '.$cnpj.' já existe.","unica","erro","'.Sessao::getId().'");
                    </script>';
                    $html->formCgm($dadosCgm,$_SERVER['PHP_SELF'],0);
            }
        } elseif ($pessoa == "outros") {
            if ($objCgm->incluiCgm($dadosCgm)) {
                //Insere auditoria
                $audicao = new auditoriaLegada;
                $audicao->setaAuditoria(Sessao::read('numCgm'), Sessao::read('acao'), $dadosCgm['numCgm']);
                $audicao->insereAuditoria();
                alertaAviso($_SERVER['PHP_SELF']."?controle=0&pessoa=".$pessoa,"CGM ".$dadosCgm['numCgm'],"incluir","aviso");
            } else {
                $stMensagem = $objCgm->stErro;
                Sessao::write('dadosCgm', $dadosCgm);
                alertaAviso($_SERVER['PHP_SELF']."?controle=5&pessoa=".$pessoa,"CGM $stMensagem","n_incluir","erro");
            }
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
                    $js .= "f.".$campoMunicipio.".options[".$iCont++."] = new Option('".$nomg_municipio."','".$codg_municipio."');\n";
                }
                if ($iCont > 1) {
                   $js .= "f.".$campoMunicipio.".disabled = false;\n";
                   $js .= "f.".$campoMunicipio.".focus();\n";
                }
                $dbEmp->limpaSelecao();
                $dbEmp->fechaBD();
            } else {
                $js .= "limpaSelect(f.".$m.",1);\n";
            }
            executaFrameOculto($js);
    break;

    case 2001:
        $stJs .= 'f.inNumLogradouroCorresp.value = "";';
        $stJs .= 'f.inNumLogradouroCorresp.focus();';
        $stJs .= 'd.getElementById("campoInnerLogrCorresp").innerHTML = "&nbsp;";';
        $stJs .= 'd.getElementById("stMunicipioCorresp").innerHTML = "&nbsp;";';
        $stJs .= 'd.getElementById("stEstadoCorresp").innerHTML = "&nbsp;";';
        $stJs .= 'f.inCodigoBairroCorresp.value = "";';
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

        $stJs .= "limpaSelect(f.cmbBairro,0); \n\r";
        $stJs .= "limpaSelect(f.cmbCEP,0); \n\r";
        $stJs .= "f.cmbBairro[0] = new Option('Selecione','', 'selected');\n\r";
        $stJs .= "f.cmbCEP[0] = new Option('Selecione','', 'selected');\n\r";

        SistemaLegado::executaFrameOculto($stJs);

    break;
    case 5: /** Monta o formulário com os dados do CGM escolhido **/

        $html->formCgm(Sessao::read('dadosCgm'),$PHP_SELF,0);
        Sessao::remove('dadosCgm');

    break;

}//Fim switch

include '../../../framework/include/rodape.inc.php'; //Insere o fim da página html

?>
