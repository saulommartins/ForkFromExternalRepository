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
  * Data de Criação: 18/09/2007

  * @author Analista: Gelson W. Gonçalves
  * @author Desenvolvedor: Henrique Boaventura

  * @package URBEM
  * @subpackage

  * $Id: OCManterBem.php 65922 2016-06-29 21:08:01Z carlos.silva $

  * Casos de uso: uc-03.01.06
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CAM_GP_PAT_MAPEAMENTO.'TPatrimonioBem.class.php';
include_once CAM_GP_PAT_MAPEAMENTO."TPatrimonioTipoNatureza.class.php";

switch ($request->get('stCtrl')) {

    case 'buscaPopupTipoBaixa':
        $stMsgErro = "";
        $inCodigo  = str_replace(".","",$request->get('inCodigo'));
        
        if ($request->get('inTipoBaixa') == '') {
            $stMsgErro = 'Prencha o campo Tipo de baixa';
        }
        
        //se for vazio, limpa os campos
        if (trim($inCodigo) != '') {
            
            $obTPatrimonioBem         = new TPatrimonioBem();
            $oTPatrimonioTipoNatureza = new TPatrimonioTipoNatureza();
            $inTipoBaixa = null;
            
            $boMostrarDescricao = $request->get('boMostrarDescricao');

            # Validação para verificar se o item existe e não está baixado.
            if (empty($stMsgErro)) {
                
                if ($request->get('boBemBaixado') == 'false') {
                    $stFiltro .= ' AND NOT EXISTS
                                       (
                                            SELECT  1
                                              FROM  patrimonio.bem_baixado
                                             WHERE  bem_baixado.cod_bem = bem.cod_bem
                                       ) ';
                }

                $obTPatrimonioBem->setDado( 'cod_bem', $inCodigo );
                $obTPatrimonioBem->recuperaRelacionamento( $rsBem, $stFiltro );

                //Verifica se bem possui Tipo de Natureza Configurado
                if ($rsBem->getCampo('codigo') == 0) {
                    $stMsgErro = 'Tipo de natureza '.$rsBem->getCampo('codigo') .' - '.$rsBem->getCampo('descricao_natureza') .'. É necessário configurar o Tipo de Natureza.';
                }
                
                // verifica se o usuário setou algum tipo de baixa e se o bem pertence a configuração de bens Imóveis
                if ( $request->get('inTipoBaixa') != 0 && ($request->get('inTipoBaixa') == 1 || $request->get('inTipoBaixa') == 3 || $request->get('inTipoBaixa') == 5)) {
                    $inTipoBaixa = 2;
                    $stTipoBaixa = "Imóvel";
                
                // verifica se o bem pertence a configuração de bens Móveis
                } else if ( $request->get('inTipoBaixa') != 0 && ($request->get('inTipoBaixa') == 2 || $request->get('inTipoBaixa') == 4 || $request->get('inTipoBaixa') == 6)) {
                    $inTipoBaixa = 1;
                    $stTipoBaixa = "Móvel";
                }

                if ($rsBem->getNumLinhas() <= 0) {
                    $stMsgErro = "Esse Bem (".$inCodigo.") é Inválido ou está Baixado";
                } else if ( $request->get('inTipoBaixa') != 0 && $rsBem->getCampo('codigo') != $inTipoBaixa ) {
                    $stMsgErro = "Para realizar uma baixa de bem ".$stTipoBaixa." é necessário que o Tipo da Natureza do bem (".$inCodigo.") seja do mesmo tipo";
                }

            }

            # Caso não encontre nenhum erro, preenche os campos com os dados.
            if (empty($stMsgErro)) {
                $stJs .= "jQuery('#".$request->get('stNomCampoCod')."').val('".$rsBem->getCampo('cod_bem')."');       \n";
                $stJs .= "jQuery('#".$request->get('stIdCampoDesc')."').html(\"".$rsBem->getCampo('descricao')."\");  \n";
            } else {
                $stJs .= "alertaAviso('@".$stMsgErro.".','form','erro','".Sessao::getId()."');";
                $stJs .= "jQuery('#".$request->get('stNomCampoCod')."').val('');          \n";
                $stJs .= "jQuery('#".$request->get('stIdCampoDesc')."').html(\"&nbsp;\"); \n";
            }
        } else {
            $stJs .= "jQuery('#".$request->get('stNomCampoCod')."').val('');          \n";
            $stJs .= "jQuery('#".$request->get('stIdCampoDesc')."').html(\"&nbsp;\"); \n";
        }
        
    break;
 
    case 'buscaPopup':
        $stMsgErro = "";
        $inCodigo  = str_replace(".","",$request->get('inCodigo'));
        
        //se for vazio, limpa os campos
        if (trim($inCodigo) != '') {
            //instancia o mapeamento e procura pelo codigo passado
            $obTPatrimonioBem = new TPatrimonioBem;
            $boMostrarDescricao = $request->get('boMostrarDescricao');

            # Validação para verificar se o item existe e não está baixado.
            if (empty($stMsgErro)) {
                if ($request->get('boBemBaixado') == 'false') {
                    $stFiltro .= " AND NOT EXISTS
                                       (
                                            SELECT  1
                                              FROM  patrimonio.bem_baixado
                                             WHERE  bem_baixado.cod_bem = bem.cod_bem
                                       ) \n";
                }
                
                if ( $request->get('inCodEntidade') != "" ) {
                    $stFiltro .= " AND bem_comprado.cod_entidade = ".$request->get('inCodEntidade')." \n";
                }
                
                $obTPatrimonioBem->setDado( 'cod_bem', $inCodigo );
                $obTPatrimonioBem->recuperaRelacionamento( $rsBem, $stFiltro );

                if ($rsBem->getNumLinhas() <= 0) {
                    $stMsgErro = "Esse Bem (".$inCodigo.") é Inválido ou está Baixado";
                }
            }

            # Caso não encontre nenhum erro, preenche os campos com os dados.
            if (empty($stMsgErro)) {
                $stJs .= "$('".$request->get('stNomCampoCod')."').value = '".$rsBem->getCampo('cod_bem')."';";
                $stJs .= "$('".$request->get('stIdCampoDesc')."').innerHTML = '".addslashes(trim($rsBem->getCampo('descricao')))."';";
            } else {
                $stJs .= "alertaAviso('@".$stMsgErro.".','form','erro','".Sessao::getId()."');";
                $stJs .= "$('".$request->get('stNomCampoCod')."').value = '';";
                $stJs .= "$('".$request->get('stIdCampoDesc')."').innerHTML = '&nbsp;';";
            }
        } else {
            $stJs .= "$('".$request->get('stNomCampoCod')."').value = '';";
            $stJs .= "$('".$request->get('stIdCampoDesc')."').innerHTML = '&nbsp;';";
        }

    break;

    case 'montaPlacaIdentificacaoFiltro':
        if ($request->get('stPlacaIdentificacao') == 'sim') {
            $obTxtNumeroPlaca = new TextBox();
            $obTxtNumeroPlaca->setRotulo( 'Número da Placa' );
            $obTxtNumeroPlaca->setTitle( 'Informe o número da placa do bem.' );
            $obTxtNumeroPlaca->setName( 'stNumeroPlaca' );
            $obTxtNumeroPlaca->setNull( true );

            $obTipoBuscaNumeroPlaca = new TipoBusca( $obTxtNumeroPlaca );

            $obFormulario = new Formulario();
            $obFormulario->addComponente( $obTipoBuscaNumeroPlaca );
            $obFormulario->montaInnerHTML();

            $stJs.= "$('spnNumeroPlaca').innerHTML = '".$obFormulario->getHTML()."';";

            //se existe um bem para o código passado, preenche o componente
            if ( $rsBem->getNumLinhas() > 0 ) {
                $stJs.= "$('".$request->get('stNomCampoCod')."').value = '".$rsBem->getCampo('cod_bem')."';";
                if ($boMostrarDescricao == true) {
                    $stJs.= "$('".$request->get('stIdCampoDesc')."').innerHTML = '".addslashes(trim($rsBem->getCampo('descricao')))."';";
                }
            } else {
                $stJs.= "$('spnNumeroPlaca').innerHTML = '';";
                $stJs.= "alertaAviso('@Código do Bem inválido!','form','erro','".Sessao::getId()."');";
                $stJs.= "$('".$request->get('stNomCampoCod')."').value = '';";
                if ($boMostrarDescricao == true) {
                    $stJs.= "$('".$request->get('stIdCampoDesc')."').innerHTML = '&nbsp;';";
                }
            }

        } else {
            $stJs.= "$('".$request->get('stNomCampoCod')."').value = '';";
            if ($boMostrarDescricao == true) {
                $stJs.= "$('".$request->get('stIdCampoDesc')."').innerHTML = '&nbsp;';";
            }
        }

    break;

    # Usado quando o filtro estiver com algumas informações setadas.
    case 'desabilitaComponenteClassificacao':

        $stJs .= "jQuery('#stCodClassificacao').attr('disabled', 'disabled');";
        $stJs .= "jQuery('#inCodNatureza').attr('disabled', 'disabled');";
        $stJs .= "jQuery('#inCodGrupo').attr('disabled', 'disabled');";
        $stJs .= "jQuery('#inCodEspecie').attr('disabled', 'disabled');";

    break;

}

echo $stJs;

?>