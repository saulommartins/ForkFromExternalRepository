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
  * Página de Filtro para Imprimir Etiquetas
  * Data de Criação   : 15/10/2007

  * @author Analista:
  * @author Desenvolvedor: Bruce Cruz de Sena

  * @ignore

  * Casos de uso : 01.06.98

  $Id: imprimeRelatorioDespachos.php 61884 2015-03-12 14:34:32Z jean $

  */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkBirt.inc.php';
include_once CAM_FW_LEGADO."funcoesLegado.lib.php";

$preview = new PreviewBirt(1,5,3);
$preview->setVersaoBirt( '2.5.0' );

$preview->setTitulo ('Relatório de despachos');

$numMatricula = pegaDado("num_matricula","sw_processo_matricula","Where cod_processo = '".$_REQUEST['codProcesso' ]."' and ano_exercicio = '".$_REQUEST['anoExercicio']."' ");
$numInscricao = pegaDado("num_inscricao","sw_processo_inscricao","Where cod_processo = '".$_REQUEST['codProcesso' ]."' and ano_exercicio = '".$_REQUEST['anoExercicio']."' ");

$preview->addParametro('pNumMatricula' , $numMatricula);
$preview->addParametro('pNumInscricao' , $numInscricao);

$preview->addParametro('pCodProcesso'  , $_REQUEST['codProcesso' ]);
$preview->addParametro('pAnoExercicio' , $_REQUEST['anoExercicio']);

$preview->addAssinaturas(Sessao::read('assinaturas'));

$preview->preview();

?>
