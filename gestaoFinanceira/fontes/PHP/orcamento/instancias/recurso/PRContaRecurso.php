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
 * Filtro para busca dos recursos que não possuem contas contábeis
 *
 * @category   Urbem
 * @package    Framework
 * @author     Analista Tonismar Bernardo <tonismar.bernardo@cnm.org.br>
 * @author     Desenvolvedor Eduardo Schitz <eduardo.schitz@cnm.org.br>
 * $Id:$
*/

include '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include CAM_GF_ORC_NEGOCIO.'ROrcamentoRecurso.class.php';
include CAM_GF_CONT_NEGOCIO.'RContabilidadePlanoBanco.class.php';

//Define o nome dos arquivos PHP
$stPrograma = 'ContaRecurso';
$pgList     = 'LS'.$stPrograma.'.php';

$obROrcamentoRecurso  = new ROrcamentoRecurso;
$obRContabilidadePlanoBanco = new RContabilidadePlanoBanco;
$obErro = new Erro;

$stAcao = $request->get('stAcao');

$inCount = 0;
foreach ($_REQUEST as $stChave => $stValor) {
    if (preg_match('/^boCriarConta/', $stChave)) {
        $arValoresRecurso = explode('_', $stChave);
        $arRecurso[$inCount] = $arValoresRecurso[1];
        $inCount++;
    }
}

$obRContabilidadePlanoBanco->countContasContabeisRecursoCredor($rsCountC);
$obRContabilidadePlanoBanco->countContasContabeisRecursoDevedor($rsCountD);
$inCountContasContabeisC = $rsCountC->getCampo('num_contas');
$inCountContasContabeisD = $rsCountD->getCampo('num_contas');
$inCountContasContabeisC += COUNT($arRecurso);
$inCountContasContabeisD += COUNT($arRecurso);

if ($inCountContasContabeisC <= 99 && $inCountContasContabeisD <= 99) {
    switch ($stAcao) {
        case 'incluir':
        for ($i = 0; $i < count($arRecurso); $i++) {
            $stCampo = 'nom_recurso';
            $stTabela = 'orcamento.recurso';
            $stFiltro  = 'WHERE exercicio = '.Sessao::getExercicio().' AND ';
            $stFiltro .= 'cod_recurso = '.$arRecurso[$i];
            $stNomRecurso = SistemaLegado::pegaDado($stCampo, $stTabela, $stFiltro);

            $obRContabilidadePlanoBanco = new RContabilidadePlanoBanco;
            $obRContabilidadePlanoBanco->setCodEstrutural('2.9.3.2.0.00.00.');
            $rsProxCod = new RecordSet;
            $obRContabilidadePlanoBanco->getProximoEstruturalRecurso($rsProxCod);
            $boValidaEstruturalC = $rsProxCod->getCampo('prox_cod_estrutural');
            if (!$obErro->ocorreu()) {
                $obRContabilidadePlanoBanco->obRContabilidadeSistemaContabil->setCodSistema(4);
                $obRContabilidadePlanoBanco->obRContabilidadeClassificacaoContabil->setCodClassificacao(1);
                $boValidaEstruturalC++;
                $boValidaEstruturalC = str_pad($boValidaEstruturalC, 2, "0", STR_PAD_LEFT);
                $stCodEstruturalC = '2.9.3.2.0.00.00.'.$boValidaEstruturalC.'.00.00';
                $obRContabilidadePlanoBanco->setCodEstrutural($stCodEstruturalC);
                $obRContabilidadePlanoBanco->setNomConta($stNomRecurso);
                $obRContabilidadePlanoBanco->setExercicio(Sessao::getExercicio());
                $obRContabilidadePlanoBanco->setNatSaldo('C');
                $obRContabilidadePlanoBanco->obROrcamentoRecurso->setCodRecurso($arRecurso[$i]);
                $obRContabilidadePlanoBanco->setContaAnalitica(true);

                $obErro = $obRContabilidadePlanoBanco->salvar();
            } else {
                SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
            }

            $obRContabilidadePlanoBanco = new RContabilidadePlanoBanco;
            $obRContabilidadePlanoBanco->setCodEstrutural('1.9.3.2.0.00.00.');
            $rsProxCod = new RecordSet;
            $obRContabilidadePlanoBanco->getProximoEstruturalRecurso($rsProxCod);
            $boValidaEstruturalD = $rsProxCod->getCampo('prox_cod_estrutural');
            if (!$obErro->ocorreu()) {
                $obRContabilidadePlanoBanco->obRContabilidadeSistemaContabil->setCodSistema(4);
                $obRContabilidadePlanoBanco->obRContabilidadeClassificacaoContabil->setCodClassificacao(1);
                $boValidaEstruturalD++;
                $boValidaEstruturalD = str_pad($boValidaEstruturalD, 2, "0", STR_PAD_LEFT);
                $stCodEstruturalD = '1.9.3.2.0.00.00.'.$boValidaEstruturalD.'.00.00';
                $obRContabilidadePlanoBanco->setCodEstrutural($stCodEstruturalD);
                $obRContabilidadePlanoBanco->setNomConta($stNomRecurso);
                $obRContabilidadePlanoBanco->setExercicio(Sessao::getExercicio());
                $obRContabilidadePlanoBanco->setNatSaldo('D');
                $obRContabilidadePlanoBanco->obROrcamentoRecurso->setCodRecurso($arRecurso[$i]);
                $obRContabilidadePlanoBanco->setContaAnalitica(true);

                $obErro = $obRContabilidadePlanoBanco->salvar();
            } else {
                SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
            }
        }

        if ( !$obErro->ocorreu() ) {
            SistemaLegado::alertaAviso($pgList," &nbsp; ","incluir","aviso", Sessao::getId(), "../");
        } else {
            SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
        }
        break;
    }
} else {
    SistemaLegado::alertaAviso($pgList," Limite de Contas Excedido! ","n_incluir","erro", Sessao::getId(), "../");
}
?>
