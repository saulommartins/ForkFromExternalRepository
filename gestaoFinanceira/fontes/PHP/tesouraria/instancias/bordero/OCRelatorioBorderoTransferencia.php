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
    * Data de Criação   : 30/11/2005

    * @author Analista: Lucas Leusin Oiagen
    * @author Desenvolvedor: Jose Eduardo Porto

    * @ignore

    $Revision: 31627 $
    $Name$
    $Autor:$
    $Date: 2006-07-05 17:51:50 -0300 (Qua, 05 Jul 2006) $

    * Casos de uso: uc-02.04.20
*/

/*
$Log$
Revision 1.4  2006/07/05 20:39:07  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CAM_FW_PDF."RRelatorio.class.php"                             );
include_once( CAM_GF_TES_NEGOCIO."RTesourariaRelatorioBorderoTransferencia.class.php"  );
include_once( CAM_GF_TES_NEGOCIO."RTesourariaBoletim.class.php"  );

$obRRelatorio                          = new RRelatorio;
$obRTesourariaRelatorioBorderoTransferencia = new RTesourariaRelatorioBorderoTransferencia;

$obRTesourariaBoletim = new RTesourariaBoletim();
$obRTesourariaBoletim->addBordero();
$obRTesourariaBoletim->roUltimoBordero->setExercicio(Sessao::read('stExercicio'));
$obRTesourariaBoletim->roUltimoBordero->obROrcamentoEntidade->setCodigoEntidade(Sessao::read('inCodEntidade'));

$obErro = $obRTesourariaBoletim->roUltimoBordero->buscaProximoCodigo($boTransacao);

if (!$obErro->ocorreu()) {
    Sessao::write('inNumBordero', $obRTesourariaBoletim->roUltimoBordero->getCodBordero());
} else {
    SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
}

$obRTesourariaRelatorioBorderoTransferencia->setDadosBordero(Sessao::read('filtro'));
$obRTesourariaRelatorioBorderoTransferencia->setDadosTransferencias(Sessao::read('arItens'));

$obRTesourariaRelatorioBorderoTransferencia->geraRecordSet( $rsBorderoTransferencia );

Sessao::write('rsBorderoTransferencia',$rsBorderoTransferencia);

$obRRelatorio->executaFrameOculto( "OCGeraRelatorioBorderoTransferencia.php" );

?>
