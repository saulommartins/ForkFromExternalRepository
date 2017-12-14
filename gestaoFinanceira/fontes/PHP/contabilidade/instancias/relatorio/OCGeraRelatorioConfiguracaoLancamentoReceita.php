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
    * Página de Filtro do relatóio de Configuração de Lançamento de Receita
    * Data de Criação   : 17/11/2011

    * @author Analista Tonismar Bernardo
    * @author Desenvolvedor Davi Aroldi

    * @ignore

    $Id: OCGeraRelatorioConfiguracaoLancamentoReceita.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-02.03.18

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkBirt.inc.php';

$preview = new PreviewBirt(2,9,8);
$preview->setVersaoBirt( '2.5.0' );
$preview->setNomeRelatorio( 'relatorioConfiguracaoLancamentoReceita' );

$preview->setTitulo("Relatório Configuração de Lançamento de Receita");

/* Teste para Verificar se o inCodReceita é Pai
 *Exemplo: 9.0.0.0.0.00.00.00.00.00 é Pai de todas as receitas de começo 9.
 *Se Pai, encaminha como parametro o inCodReceita=9 (no caso do exemplo acima)
 *Senão é encaminhado como chegou aqui, sem alteração
 *
 *A PL relatorioConfiguracaoLancamentoReceita.plsql faz o tratamento
 *para verificar se o CodReceita é Pai ou não em decorrência deste OC.
*/
$inCodReceitaAux = explode(".", $_POST['inCodReceita']);
$axu=1;
for ($i=1;$i<count($inCodReceitaAux);$i++) {
    if ($inCodReceitaAux[$i]=='0'||$inCodReceitaAux[$i]=='00') {
        $axu++;
    }
}
if ($axu==count($inCodReceitaAux)) {
    $inCodReceita = $inCodReceitaAux[0];
} else {
    $inCodReceita = $_POST['inCodReceita'];
}

$preview->addParametro( 'cod_classificacao_receita', $inCodReceita );

$preview->preview();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
