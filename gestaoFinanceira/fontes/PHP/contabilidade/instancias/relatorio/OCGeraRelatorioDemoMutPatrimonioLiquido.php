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
    * Data de Criação   : 12/05/2005

    * @author Analista: Diego Barbosa Victoria
    * @author Desenvolvedor: Cleisson da Silva Barboza

    * @ignore

    * $Id: OCDemoRecDespExtraOrcamento.php 46609 2012-05-18 13:07:51Z tonismar $

    * Casos de uso: uc-02.02.15
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkBirt.inc.php';
include_once ( CAM_GF_ORC_MAPEAMENTO."TOrcamentoEntidade.class.php"                                    );
include_once (CAM_FW_LEGADO."funcoesLegado.lib.php"    );

$obTOrcamentoEntidade = new TOrcamentoEntidade();
$obTOrcamentoEntidade->setDado( 'exercicio'   , Sessao::getExercicio() );
$obTOrcamentoEntidade->recuperaEntidades( $rsEntidade, "and e.cod_entidade in (".implode(',',$_REQUEST['inCodEntidade']).")" );

$preview = new PreviewBirt(2,9,11);
$preview->setTitulo('Demonstração das Mutações do Patrimônio Líquido');
$preview->setVersaoBirt( '2.5.0' );

$preview->addParametro ( 'exercicio', Sessao::getExercicio() );
$preview->addParametro ( 'entidade', implode(',', $_REQUEST['inCodEntidade'] ) );
$preview->addParametro ( 'exercicio_anterior', (Sessao::getExercicio() - 1));

if ( count($_REQUEST['inCodEntidade']) == 1 ) {

    $preview->addParametro( 'nom_entidade', $rsEntidade->getCampo('nom_cgm') );

} else {
    $rsEntidade->setPrimeiroElemento();

    while ( !$rsEntidade->eof() ) {
        if (preg_match("/prefeitura.*/i", $rsEntidade->getCampo( 'nom_cgm' ))) {
            $preview->addParametro( 'nom_entidade', $rsEntidade->getCampo('nom_cgm'));
            break;
        }
        $rsEntidade->proximo();
    }
}

//$preview->addParametro( 'periodicidade', $_REQUEST['inPeriodicidade'] );

//adicionada data de emissão no rodapé do relatório
$dtDataEmissao = date('d/m/Y');
$dtHoraEmissao = date('H:i');
$stDataEmissao = "Data da emissão ".$dtDataEmissao." e hora da emissão ".$dtHoraEmissao;

$preview->addParametro( 'data_emissao', $stDataEmissao );

$preview->addParametro( 'dt_inicial', $_REQUEST['stDataInicial'] );
$preview->addParametro( 'dt_final', $_REQUEST['stDataFinal'] );
$preview->addParametro('data_inicial_nota',implode('-',array_reverse(explode('/', $_POST['stDataInicial']))));
$preview->addParametro('data_final_nota'  ,implode('-',array_reverse(explode('/', $_POST['stDataFinal']))));
$preview->addAssinaturas(Sessao::read('assinaturas'));

$preview->preview();
?>
