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
    * Data de Criação   : 03/09/2004

    * @author Eduardo Martins

    * @ignore

    $Revision: 30859 $
    $Name$
    $Author: vandre $
    $Date: 2006-08-08 14:53:12 -0300 (Ter, 08 Ago 2006) $

    * Casos de uso :uc-04.02.03

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CAM_FW_PDF."RRelatorio.class.php"  );
include_once( CAM_GRH_CAL_NEGOCIO."RCalendarioRelatorioFeriado.class.php" );

$obRRelatorio        = new RRelatorio;
$obRRelatorioFeriado = new RRelatorioFeriado;

$arSessaoFiltroRelatorio = Sessao::read('filtroRelatorio');

$obRRelatorioFeriado->obRFeriado->setDtInicial  ( $arSessaoFiltroRelatorio['dtDataInicial'] );
$obRRelatorioFeriado->obRFeriado->setDtFinal    ( $arSessaoFiltroRelatorio['dtDataFinal']   );
$obRRelatorioFeriado->obRFeriado->setTipo       ( $arSessaoFiltroRelatorio['stTipo']        );
$obRRelatorioFeriado->obRFeriado->setAbrangencia( $arSessaoFiltroRelatorio['stAbrangencia'] );

Sessao::remove('transf50');
Sessao::remove('transf51');
Sessao::remove('transf52');
Sessao::remove('transf53');

$obRRelatorioFeriado->geraRecordSet( $rsFeriadoVariavel , $rsFeriadoFixo,$rsPontoFacultativo,$rsDiaCompensado,"dt_feriado" );

switch ( $obRRelatorioFeriado->obRFeriado->getTipo()) {
  case 'Fixo':
    Sessao::write('transf50', $rsFeriadoFixo);
  break;
  case 'Variavel':
    Sessao::write('transf51', $rsFeriadoVariavel);
  break;
  case 'Ponto facultativo':
    Sessao::write('transf52', $rsPontoFacultativo);
  break;
  case 'Dia compensado':
    Sessao::write('transf53', $rsDiaCompensado);
  break;
  case '':
    Sessao::write('transf50', $rsFeriadoFixo);
    Sessao::write('transf51', $rsFeriadoVariavel);
    if (!$obRRelatorioFeriado->obRFeriado->getAbrangencia()) {
        Sessao::write('transf52', $rsPontoFacultativo);
        Sessao::write('transf53', $rsDiaCompensado);
    }
  break;
}

$obRRelatorio->executaFrameOculto( "OCGeraRelatorioFeriado.php" );
?>
