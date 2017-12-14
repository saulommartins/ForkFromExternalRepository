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
    * Página de Formulario de Seleção de Impressora para Relatorio
    * Data de Criação   : 26/01/2006

    * @author Analista: Lucas Leusin Oiagen
    * @author Desenvolvedor: Jose Eduardo Porto

    * @ignore

    $Revision: 31732 $
    $Name$
    $Autor:$
    $Date: 2006-10-23 13:33:46 -0300 (Seg, 23 Out 2006) $

    * Casos de uso: uc-02.04.20
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CAM_FW_PDF."RRelatorio.class.php"                             );
include_once( CAM_GF_TES_NEGOCIO."RTesourariaRelatorioBorderoPagamento.class.php"  );
include_once( CAM_GF_TES_NEGOCIO."RTesourariaBoletim.class.php"  );

$obRRelatorio                          = new RRelatorio;
$obRTesourariaRelatorioBorderoPagamento = new RTesourariaRelatorioBorderoPagamento;
$arFiltro = Sessao::read('filtroRelatorio');

$obRTesourariaBoletim = new RTesourariaBoletim();
$obRTesourariaBoletim->addBordero();
$obRTesourariaBoletim->roUltimoBordero->setExercicio($arFiltro['stExercicio'] );
$obRTesourariaBoletim->roUltimoBordero->obROrcamentoEntidade->setCodigoEntidade($arFiltro['inCodEntidade']);

$obErro = $obRTesourariaBoletim->roUltimoBordero->buscaProximoCodigo($boTransacao);

if (!$obErro->ocorreu()) {
    $arFiltro['inNumBordero'] = $obRTesourariaBoletim->roUltimoBordero->getCodBordero();
} else {
    $nomAcao = SistemaLegado::pegaDado("nom_acao","administracao.acao"," where cod_acao = ".Sessao::read('acao'));
    SistemaLegado::exibeAviso(urlencode("Erro ao executar ação: ".$nomAcao." (".$obErro->getDescricao().")"),"","erro");
}

$obRTesourariaRelatorioBorderoPagamento->setDadosBordero($arFiltro);
$obRTesourariaRelatorioBorderoPagamento->setDadosPagamento(Sessao::read('arItens'));

if ( Sessao::getExercicio() >= '2015' )
    $obRTesourariaRelatorioBorderoPagamento->geraRecordSetBorderoPagamento2015( $rsBorderoPagamento );
else
    $obRTesourariaRelatorioBorderoPagamento->geraRecordSet( $rsBorderoPagamento );

Sessao::write('arDados', $rsBorderoPagamento);

$obRRelatorio->executaFrameOculto( "OCGeraRelatorioBorderoPagamento.php" );

?>