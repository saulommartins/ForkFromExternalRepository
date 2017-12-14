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
    * Página Oculta de Tipo Documento Digital
    * Data de criação   : 05/06/2016

    * @author Michel Teixeira

    * @ignore

    $Id: OCManterTipoDocumentoDigital.php 66021 2016-07-07 18:41:02Z michel $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CAM_GRH_PES_MAPEAMENTO."TPessoalTipoDocumentoDigital.class.php";
include_once CAM_GRH_PES_MAPEAMENTO."TPessoalServidorDocumentoDigital.class.php";

function excluirTipoDocumentoDigital($inCodTipo)
{
    $stJs = '';
    $obErro = new Erro;
    $obTransacao = new Transacao();
    $boFlagTransacao = false;
    $obErro = $obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );

    $obTPessoalServidorDocumentoDigital = new TPessoalServidorDocumentoDigital();
    $obTPessoalTipoDocumentoDigital = new TPessoalTipoDocumentoDigital();

    if ( !$obErro->ocorreu() ) {
        $obErro = $obTPessoalServidorDocumentoDigital->recuperaTodos($rsServidorDocDigital, " WHERE cod_tipo = ".$inCodTipo, "", $boTransacao);

        if ( !$obErro->ocorreu() ) {
            $stMensagem = '';
            if ( $rsServidorDocDigital->getNumLinhas() > 0 ) {
                $obErro->setDescricao('Este tipo de documento digital está sendo usado!');
            }

            if ( !$obErro->ocorreu() ) {
                $arArquivosDocumentos = ( is_array( Sessao::read('arArquivosDocumentos') ) ) ? Sessao::read('arArquivosDocumentos') : array();
                foreach($arArquivosDocumentos AS $chave => $arquivo){
                    if($arquivo['inTipoDocDigital'] == $inCodTipo){
                        $obErro->setDescricao('Este tipo de documento digital está sendo usado!');
                        break;
                    }
                }
            }

            if ( !$obErro->ocorreu() ) {
                $obTPessoalTipoDocumentoDigital->setDado('cod_tipo', $inCodTipo);

                $obErro = $obTPessoalTipoDocumentoDigital->exclusao($boTransacao);
            }
        }
    }

    if ( $obErro->ocorreu() ) {
        $stJs .= "alertaAviso('".$obErro->getDescricao()."','n_incluir','erro','".Sessao::getId()."',''); \n";
        $stJs .= "mudaTelaPrincipal('".CAM_GRH_PES_INSTANCIAS."servidor/LSManterTipoDocumentoDigital.php'); \n";
    } else {
        $stJs .= atualizaCombo();
        $stJs .= "alertaAviso('Requisito excluído com sucesso!','incluir','aviso','".Sessao::getId()."');  \n";
        $stJs .= "mudaTelaPrincipal('".CAM_GRH_PES_INSTANCIAS."servidor/FMManterTipoDocumentoDigital.php'); \n";
    }

    $obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $obTPessoalTipoDocumentoDigital );

    return $stJs;
}

function atualizaCombo()
{
    $obTPessoalTipoDocumentoDigital = new TPessoalTipoDocumentoDigital();
    $obTPessoalTipoDocumentoDigital->recuperaTodos($rsTipoDocDigital, "", " ORDER BY descricao ");

    $stJs  = "var lengthSelect; \n";
    $stJs .= "window.parent.window.opener.document.getElementById('inTipoDocDigital').length = 0; \n";
    $stJs .= "lengthSelect = window.parent.window.opener.document.getElementById('inTipoDocDigital').length; \n";
    $stJs .= "window.parent.window.opener.document.getElementById('inTipoDocDigital').options[lengthSelect] = new Option(\"Selecione\", ''); \n";

    foreach ($rsTipoDocDigital->arElementos as $tipo) {
        $stJs .= "lengthSelect = window.parent.window.opener.document.getElementById('inTipoDocDigital').length; \n";
        $stJs .= "window.parent.window.opener.document.getElementById('inTipoDocDigital').options[lengthSelect] = new Option(\"".$tipo['descricao']."\", ".$tipo['cod_tipo']."); \n";
    }

    return $stJs;
}

switch ($request->get('stCtrl')) {
    case 'excluirTipoDocumentoDigital':
        $stJs = excluirTipoDocumentoDigital($request->get('cod_tipo'));
    break;
}

if ($stJs) {
   echo $stJs;
}
?>
