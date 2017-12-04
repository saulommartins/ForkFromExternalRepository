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
    * Arquivo de instância para Relatorio.
    * Data de Criação: 14/03/2008

    * @author Rodrigo Soares Rodrigues

    * Casos de uso: uc-01.06.98

    $Id: reciboProcesso.php 62581 2015-05-21 14:05:03Z michel $

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkBirt.inc.php';
include_once (CAM_FW_LEGADO."funcoesLegado.lib.php"      );

$preview = new PreviewBirt(1,5,4);
$preview->setVersaoBirt( '2.5.0' );
$preview->setNomeArquivo('reciboProcesso');

$numMatricula = pegaDado("num_matricula","sw_processo_matricula","Where cod_processo = '".$_REQUEST['codProcesso' ]."' and ano_exercicio = '".$_REQUEST['anoExercicio']."' ");
$numInscricao = pegaDado("num_inscricao","sw_processo_inscricao","Where cod_processo = '".$_REQUEST['codProcesso' ]."' and ano_exercicio = '".$_REQUEST['anoExercicio']."' ");
$stEntidadePrincipal = SistemaLegado::pegaConfiguracao("nom_prefeitura",2,Sessao::getExercicio());

$preview->addParametro ( 'pNumMatricula' , $numMatricula );
$preview->addParametro ( 'pNumInscricao' , $numInscricao );

$preview->addParametro ( 'pExercicioSessao' , Sessao::getExercicio() );

$preview->addParametro ( 'pCodProcesso'  , $_REQUEST['codProcesso' ] );
$preview->addParametro ( 'pAnoExercicio' , $_REQUEST['anoExercicio'] );

$cod_municipio = pegaConfiguracao("cod_municipio");
$codUf = pegaConfiguracao("cod_uf");
$preview->addParametro ( 'pCodMunicipio' , $cod_municipio );
$preview->addParametro ( 'pCodUf' , $codUf );
$preview->addParametro ('pEntidadePrincipal'     , $stEntidadePrincipal);

$stDataHoje = dataExtenso(date("Y-m-d"));
$preview->addParametro ('pDataHoje', $stDataHoje);

$preview->addParametro ('centroCusto', SistemaLegado::pegaConfiguracao("centro_custo", 5));

$preview->addAssinaturas(Sessao::read('assinaturas'));

$preview->preview();
