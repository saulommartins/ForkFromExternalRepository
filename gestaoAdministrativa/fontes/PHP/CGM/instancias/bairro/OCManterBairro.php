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

    * Página de processamento oculto para o cadastro de bairro
    * Data de Criação   : 23/07/2015
    * @author Analista: Gelson Wolowski Gonçalves
    * @author Desenvolvedor: Evandro Melos    
    * $Id: $    
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GT_CIM_NEGOCIO."RCIMBairro.class.php"        );
include_once ( CAM_GT_CIM_NEGOCIO."RCIMLogradouro.class.php"    );
include_once ( CAM_GT_CIM_NEGOCIO."RCIMConfiguracao.class.php"  );

$stCtrl = $_REQUEST['stCtrl'];

//Record Sets
$rsMunicipios = new RecordSet;
$rsUFs        = new RecordSet;

function carregaEstadoMunicipio()
{
    $obRCIMConfiguracao = new RCIMConfiguracao;
    $obRCIMConfiguracao->consultarConfiguracao();
    $obRCIMConfiguracao->listaDadosMunicipio( $arConfiguracao );

    $obRCIMBairro = new RCIMBairro;
    $obRCIMBairro->listarUF( $rsUFs );
    $obRCIMBairro->setCodigoUF( $arConfiguracao['cod_uf']);
    $obRCIMBairro->listarMunicipios( $rsMunicipios );
            
    $stJs = "";
    $stJs .= " jq('#stNomeBairro').focus();";
    if ( $rsUFs->getNumLinhas() > 0 ) {
        foreach ($rsUFs->getElementos() as $key => $value) {
            $value['nom_uf'] = addslashes($value['nom_uf']);
            $stJs .= " jq('#cmbUF').append(new Option('".$value['nom_uf']."','".$value['cod_uf']."') ); ";                    
        }
        //se vem valor pelo request ja seleciona
        if ($_REQUEST['inCodUF']) {
            $stJs .= " jq('#inCodUF').val(".$_REQUEST['inCodUF']."); ";
            $stJs .= " jq('#cmbUF').val(".$_REQUEST['inCodUF']."); ";
        }else{
            $stJs .= " jq('#inCodUF').val('".$arConfiguracao['cod_uf']."'); ";
            $stJs .= " jq('#cmbUF').val('".$arConfiguracao['cod_uf']."'); ";  
        }
    }
            
    if ( $rsMunicipios->getNumLinhas() > 0 ) {
        foreach ($rsMunicipios->getElementos() as $key => $value) {
            $value['nom_municipio'] = addslashes($value['nom_municipio']);
            $stJs .= " jq('#cmbMunicipio').append(new Option('".$value['nom_municipio']."','".$value['cod_municipio']."') ); ";                    
        }
        //se vem valor pelo request ja seleciona
        if ($_REQUEST['inCodMunicipio']) {
            $stJs .= " jq('#inCodMunicipio').val(".$_REQUEST['inCodMunicipio']."); ";
            $stJs .= " jq('#cmbMunicipio').val(".$_REQUEST['inCodMunicipio']."); ";
        }else{
            $stJs .= " jq('#inCodMunicipio').val('".$arConfiguracao['cod_municipio']."'); ";
            $stJs .= " jq('#cmbMunicipio').val('".$arConfiguracao['cod_municipio']."'); ";
        }
    }

    return $stJs;
}

function preencheMunicipio()
{
    $stJs  = " jq('#inCodMunicipio').val(''); \n";
    $stJs .= " jq('#cmbMunicipio').empty().append(new Option('Selecione','')); \n";        
    if ($_REQUEST["inCodUF"]) {
        $obRCIMBairro = new RCIMBairro;
        $obRCIMBairro->setCodigoUF( $_REQUEST["inCodUF"]);
        $obRCIMBairro->listarMunicipios( $rsMunicipios );
        if ( $rsMunicipios->getNumLinhas() > 0 ) {
            foreach ($rsMunicipios->getElementos() as $key => $value) {
                $value['nom_municipio'] = addslashes($value['nom_municipio']);
                $stJs .= " jq('#cmbMunicipio').append(new Option('".$value['nom_municipio']."','".$value['cod_municipio']."') ); ";                    
            }
        }
    }        

    return $stJs;
}

switch ($stCtrl) {
    case "preencheMunicipio":
        $stJs = preencheMunicipio();
    break;

    case "carregarDadosIncluir":
    case "carregarDadosAlterar":
    case "carregarDadosConsultar":
        $stJs = carregaEstadoMunicipio();
    break;
}

if ($stJs) {
    echo $stJs;
}

?>
