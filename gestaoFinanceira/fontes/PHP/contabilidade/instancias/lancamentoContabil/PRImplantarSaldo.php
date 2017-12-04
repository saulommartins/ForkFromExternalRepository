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
    * Página de Processamento para Implantar Saldo
    * Data de Criação   : 22/11/2004

    * @author Analista: Jorge B. Ribarr
    * @author Desenvolvedor: Gelson W. Gonçalves

    * @ignore

    * $Id: PRImplantarSaldo.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-02.02.04
*/

include '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include( CAM_GF_CONT_NEGOCIO."RContabilidadeLancamentoValor.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "ImplantarSaldo";
$pgFilt    = "FL".$stPrograma.".php";
$pgList    = "LS".$stPrograma.".php";
$pgForm    = "FM".$stPrograma.".php";
$pgProc    = "PR".$stPrograma.".php";
$pgOcul    = "OC".$stPrograma.".php";

$obRContabilidadeLancamentoValor = new RContabilidadeLancamentoValor;

/*
 * Rotina de Inclusao
 */

$obRContabilidadeLancamentoValor->obRContabilidadePlanoContaAnalitica->setExercicio    ( Sessao::getExercicio() );
$obRContabilidadeLancamentoValor->obRContabilidadePlanoContaAnalitica->setCodEstrutural( $_POST[ 'stCodEstrutural' ] );
$obRContabilidadeLancamentoValor->obRContabilidadePlanoContaAnalitica->setCodEstruturalInicial( $_POST[ 'stCodEstruturalInicial' ] );
$obRContabilidadeLancamentoValor->obRContabilidadePlanoContaAnalitica->setCodEstruturalFinal( $_POST[ 'stCodEstruturalFinal' ] );
$obRContabilidadeLancamentoValor->obRContabilidadePlanoContaAnalitica->setCodPlanoInicial( $_POST[ 'inCodPlanoInicial' ] );
$obRContabilidadeLancamentoValor->obRContabilidadePlanoContaAnalitica->setCodPlanoFinal( $_POST[ 'inCodPlanoFinal' ] );
$obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->obROrcamentoEntidade->setCodigoEntidade( $_POST[ 'inCodEntidade' ] );
$obRContabilidadeLancamentoValor->obRContabilidadePlanoContaAnalitica->setCodGrupo( $_POST[ 'inCodGrupo' ] );
$obRContabilidadeLancamentoValor->listarLoteImplantacaoPlanoBanco( $rsContas ) ;

$count=0;
for ($i=0; $i<count($rsContas->arElementos);$i++) {
   if ($rsContas->arElementos[$i]['plano_banco']!='NOK') {
      $rsContas->arElementos[$count] = $rsContas->arElementos[$i];
      $count++;
   }
}
$rsContas->inNumLinhas=$count;
$totalOrig=count($rsContas->arElementos);
for ($count2=$count; $totalOrig>$count2;$totalOrig--) {
   unset($rsContas->arElementos[$totalOrig-1]);
}

while ( !$rsContas->eof() ) {
    $nuValor = 'nuValor_'.$rsContas->getCorrente();
    if ( trim($_POST[$nuValor]) != '' ) {
        $nuValorImplantacao = str_replace('.','',$_POST[$nuValor]);
        $nuValorImplantacao = str_replace(',','.',$nuValorImplantacao);
        $arImplantaSaldo[$rsContas->getCampo( "cod_plano" )."-".$rsContas->getCampo( "sequencia" )]=$nuValorImplantacao;
    }
    $rsContas->proximo();
}

$obRContabilidadeLancamentoValor->setImplantaSaldo($arImplantaSaldo);
$obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->setExercicio( Sessao::getExercicio() );
$obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->setDtLote( "01/01/".Sessao::getExercicio() );
$obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->obROrcamentoEntidade->setCodigoEntidade( $_POST['inCodEntidade'] );
$obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->setTipo('I');
$obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->setNomLote('Implantação de Saldo');
$obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeHistoricoPadrao->setCodHistorico(1);

$obErro = $obRContabilidadeLancamentoValor->implantarSaldo();
if ( !$obErro->ocorreu() ) {
    SistemaLegado::alertaAviso($pgFilt, "1 - ".($obRContabilidadeLancamentoValor->obRContabilidadeLancamento->getSequencia() ? $obRContabilidadeLancamentoValor->obRContabilidadeLancamento->getSequencia() : "0")."", "incluir", "aviso", Sessao::getId(), "../");
} else
    SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");

?>
