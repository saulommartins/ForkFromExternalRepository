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
* Arquivo instância para popup de fornecedor
* Data de Criação: 12/09/2006

* @author Analista: Diego Barbosa Victoria
* @author Desenvolvedor: Diego Barbosa Victoria

$Id: OCProcurarFornecedor.php 59612 2014-09-02 12:00:51Z gelson $

$Revision: 19226 $
$Name$
$Author: rodrigo $
$Date: 2007-01-10 16:07:45 -0200 (Qua, 10 Jan 2007) $

Casos de uso: uc-03.04.03
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CAM_GP_COM_MAPEAMENTO ."TComprasFornecedor.class.php"               				 );

switch ($_GET['stCtrl']) {
    case 'buscaPopup':
        $stJs = '';
    $stNomCGM = '';
        if ($_GET [ $_GET['stNomCampoCod'] ] != "") {

            $obMapeamento = new TComprasFornecedor();
            $stSql = ' AND cgm_fornecedor = '.$request->get($request->get('stNomCampoCod'));
            $stSql.= ' AND forn.ativo     = true ';
            $stSql.= "     ";

            if ($_GET['stTipoConsulta'] == 'certificados') {
                $stSql .= "  and
                                exists (  select 1
                             from
                                   licitacao.participante_certificacao
                              where
                                  participante_certificacao.cgm_fornecedor = forn.cgm_fornecedor  )  ";
            }

            $obMapeamento->recuperaRelacionamento($rsCGM, $stSql);

            $stNomCGM = $rsCGM->getCampo('nom_cgm');
            if ( trim($stNomCGM) == '' ) {
                $stJs .= "f.".$request->get('stNomCampoCod').".value='';";
                $stJs .= "jq('stNomCGM').html('&nbsp;');";
                $stJs .= "alertaAviso('@Número do Fornecedor (". $request->get($request->get('stNomCampoCod')) .") não encontrado.', 'form','erro','".Sessao::getId()."');";
                //sistemaLegado::executaFrameOculto( $stJs );
                echo $stJs;
                exit;
            }
        }
    $stNomCampoCod = $request->get('stNomCampoCod') ? $request->get('stNomCampoCod') : '';
    $stIdCampoDesc = $request->get('stIdCampoDesc') ? $request->get('stIdCampoDesc') : '';

        $stJs .= "retornaValorBscInner( '".$stNomCampoCod."', '".$stIdCampoDesc."', 'frm', '".$stNomCGM."');";
        echo $stJs;
    break;

}

?>
