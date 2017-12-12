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
    * Página de Listagem de Organograma
    * Data de criação : 06/06/2005

    * @author Programador: Vandré Miguel Ramos

    $Revision: 30547 $
    $Name$
    $Author: vandre $
    $Date: 2006-08-08 14:53:12 -0300 (Ter, 08 Ago 2006) $

    Caso de uso: uc-04.04.08
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';

$rsLista = new RecordSet;

$obRPessoalAssentamento->addPessoalCausaRescisao();
$obRPessoalAssentamento->roUltimoPessoalCausaRescisao->listarCausa($rsLista,$stOrder,$boTransacao);

$obLista = new Lista;

$obLista->setRecordSet( $rsLista );
$obLista->setMostraPaginacao( false );

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Código" );
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Descrição" );
$obLista->ultimoCabecalho->setWidth( 70 );
$obLista->commitCabecalho();

$obChkRescisao = new CheckBox;
$obChkRescisao->setName           ( "inCodAbaRescisao_[cod_causa_rescisao]_"  );
$obChkRescisao->setValue          ( "true");
//obChkRescisao->obEvento->setOnClick( 'validaPrevidencia()' );

$obLista->addDadoComponente( $obChkRescisao );
$obLista->ultimoDado->setAlinhamento("CENTRO");
$obLista->ultimoDado->setCampo( "booleano" );
$obLista->commitDadoComponente();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("DIREITA");
$obLista->ultimoDado->setCampo( "cod_causa_rescisao" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("ESQUERDA");
$obLista->ultimoDado->setCampo( "descricao" );
$obLista->commitDado();

$obLista->montaHTML();
$stHtml = $obLista->getHTML();
$stHtml = str_replace("\n","",$stHtml);
$stHtml = str_replace("  ","",$stHtml);
$stHtml = str_replace("'","\\'",$stHtml);

// preenche a lista com innerHTML

$stJs .= "d.getElementById('spnRescisao').innerHTML = '".$stHtml."';";
sistemaLegado::executaFrameOculto($stJs);

?>
