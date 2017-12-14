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
    * Pagina de Lista de Resumo do Fechamento da Baixa Manual
    * Data de Criação   : 11/05/2006

    * @author Analista: Fabio Bertoldi
    * @author Desenvolvedor: Fernando Piccini Cercato

    * @ignore

    * $Id: LSResumoFechamentoBaixaManual.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-05.03.10
*/

/*
$Log$
Revision 1.4  2006/09/15 10:55:12  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';

//Define o nome dos arquivos PHP
$pgProx = "FMResumoFechamentoBaixaManual.php";

// instancia regra de lancamento
$stAcao = "Visualizar";
$stLink .= "&Voltar=1";

$rsLista = new RecordSet;
$rsLista->preenche( Sessao::read( 'fechamento' ) );

$obLista = new Lista;
$obLista->setRecordSet ( $rsLista  );
$obLista->setTitulo ( "Resumo do Fechamento da Baixa Manual" );

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Lote");
$obLista->ultimoCabecalho->setWidth( 20 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Banco");
$obLista->ultimoCabecalho->setWidth( 20 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Agência");
$obLista->ultimoCabecalho->setWidth( 25 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();

$obLista->addDado();
$obLista->ultimoDado->setCampo          ( "[cod_lote] - [pagamento]" );
$obLista->ultimoDado->setAlinhamento    ( "ESQUERDA" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setCampo          ( "[cod_banco] - [nom_banco]" );
$obLista->ultimoDado->setAlinhamento    ( "ESQUERDA" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setCampo          ( "[cod_agencia] - [nom_agencia]" );
$obLista->ultimoDado->setAlinhamento    ( "ESQUERDA" );
$obLista->commitDado();

$obLista->addAcao();
$obLista->ultimaAcao->setAcao( $stAcao );
$obLista->ultimaAcao->addCampo( "&cod_lote", "cod_lote" );
$obLista->ultimaAcao->addCampo( "&exercicio", "exercicio" );
$obLista->ultimaAcao->addCampo( "pagamento", "pagamento" );
$obLista->ultimaAcao->setLink( $pgProx."?".Sessao::getId().$stLink );
$obLista->commitAcao();

$obLista->show();

?>
