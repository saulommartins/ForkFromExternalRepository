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
    * Regra de negocio para anexo 2 Receita
    * Data de Criação: 17/05/2005

    * @author Analista: Diego Barbosa
    * @author Desenvolvedor: Cleisson da silva Barboza

    * @package URBEM
    * @subpackage Regra

    $Revision: 32085 $
    $Name$
    $Author: cleisson $
    $Date: 2006-07-05 17:51:50 -0300 (Qua, 05 Jul 2006) $

    * Casos de uso: uc-02.01.10

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CLA_PERSISTENTE_RELATORIO);
include_once( CAM_GF_ORC_NEGOCIO."ROrcamentoEntidade.class.php"                  );
include_once( CAM_GF_ORC_MAPEAMENTO."FOrcamentoSomatorioReceitaBalanco.class.php" );

class ROrcamentoRelatorioAnexo2ReceitaBalanco extends PersistenteRelatorio
{
/**
    * @var Object
    * @access Private
*/
var $obFOrcamentoSomatorioReceitaBalanco;
var $stEntidades;
var $inExercicio;
var $dtDataInicial;
var $dtDataFinal;
var $obREntidade;

/**
     * @access Public
     * @param Object $valor
*/
function setFOrcamentoSomatorioReceitaBalanco($valor) { $this->obFOrcamentoSomatorioReceitaBalanco  = $valor; }
function setEntidades($valor) { $this->stEntidades                          = $valor; }
function setExercicio($valor) { $this->inExercicio                          = $valor; }
function setDataInicial($valor) { $this->dtDataInicial                        = $valor; }
function setDataFinal($valor) { $this->dtDataFinal                          = $valor; }
function setREntidade($valor) { $this->obREntidade                          = $valor; }
/**
     * @access Public
     * @param Object $valor
*/
function getFOrcamentoSomatorioReceitaBalanco() { return $this->obFOrcamentoSomatorioReceitaBalanco   ; }
function getEntidades() { return $this->stEntidades                           ; }
function getExercicio() { return $this->inExercicio                           ; }
function getDataInicial() { return $this->dtDataInicial                         ; }
function getDataFinal() { return $this->dtDataFinal                           ; }
function getREntidade() { return $this->obREntidade                           ; }

/**
    * Método Construtor
    * @access Private
*/
function ROrcamentoRelatorioAnexo2ReceitaBalanco()
{
    $this->setFOrcamentoSomatorioReceitaBalanco ( new FOrcamentoSomatorioReceitaBalanco   );
    $this->obREntidade                  = new ROrcamentoEntidade;
    $this->obREntidade->obRCGM->setNumCGM( Sessao::read('numCgm')         );
}

/**
    * Método abstrato
    * @access Public
*/
function geraRecordSet(&$rsRecordSet , &$rsRecordSet2, $stOrder = "")
{
    $rsRecordSet = new RecordSet;

    $this->obFOrcamentoSomatorioReceitaBalanco->setDado ("exercicio", $this->inExercicio);
    $this->obFOrcamentoSomatorioReceitaBalanco->setDado ("stEntidades", $this->stEntidades);
    $this->obFOrcamentoSomatorioReceitaBalanco->setDado ("dtDataInicial", $this->dtDataInicial);
    $this->obFOrcamentoSomatorioReceitaBalanco->setDado ("dtDataFinal", $this->dtDataFinal);

    $stFiltro = " WHERE valor <> 0";

    $obErro = $this->obFOrcamentoSomatorioReceitaBalanco->recuperaTodos( $rsRecordSet, $stFiltro, "" );

    $inCount = 0;
    $inCountResumoCorrente = 0;
    $inCountResumoCapital  = 0;
    $inSomaCorrentes = 0;
    $inSomaCapital   = 0;
    while ( !$rsRecordSet->eof() ) {

        $arOrcamento[$inCount]['classificacao'] = $rsRecordSet->getCampo('classificacao');
        $arOrcamento[$inCount]['descricao']     = $rsRecordSet->getCampo('descricao');
        $arOrcamento[$inCount]['alinhamento']   = $rsRecordSet->getCampo('alinhamento');

        $stColuna = trim($rsRecordSet->getCampo("coluna"));

        $stGrupo  = trim($rsRecordSet->getCampo("classificacao_reduzida"));
        $stGrupo  = explode (".", $stGrupo);
        $stGrupo  = $stGrupo[0];

        $inValor = number_format ($rsRecordSet->getCampo('valor'),2,",",".");

        if ($stColuna == 'desdobramento') {
            $arOrcamento[$inCount]['valor_d']       = $inValor;
            $arOrcamento[$inCount]['valor_f']       = '';
            $arOrcamento[$inCount]['valor_e']       = '';
         } elseif ($stColuna == 'fontes') {
            if ( ($stGrupo == 1 ) && (trim($rsRecordSet->getCampo("nivel") == 2)) ) {
                $arResumoCorrente[$inCountResumoCorrente]["indentacao"] = 1;
                $arResumoCorrente[$inCountResumoCorrente]["descricao"]  = $rsRecordSet->getCampo("descricao");
                $arResumoCorrente[$inCountResumoCorrente]["valor"]      = $inValor;
                $inSomaCorrentes = $inSomaCorrentes + $rsRecordSet->getCampo('valor');
                $inCountResumoCorrente++;
            } elseif (($stGrupo == 2) && (trim($rsRecordSet->getCampo("nivel") == 2)) ) {
                $arResumoCapital[$inCountResumoCapital]["indentacao"] = 1;
                $arResumoCapital[$inCountResumoCapital]["descricao"]  = $rsRecordSet->getCampo("descricao");
                $arResumoCapital[$inCountResumoCapital]["valor"]      = $inValor;
                $inSomaCapital = $inSomaCapital + $rsRecordSet->getCampo('valor');
                $inCountResumoCapital++;
            } elseif (($stGrupo == 7) && (trim($rsRecordSet->getCampo("nivel") == 2))) {
                $arResumoCorrente[$inCountResumoCorrente]["indentacao"] = 1;
                $arResumoCorrente[$inCountResumoCorrente]["descricao"]  = $rsRecordSet->getCampo("descricao");
                $arResumoCorrente[$inCountResumoCorrente]["valor"]      = $inValor;
                $inSomaCorrentes = $inSomaCorrentes + $rsRecordSet->getCampo('valor');
                $inCountResumoCorrente++;
            } elseif (($stGrupo == 8) && (trim($rsRecordSet->getCampo("nivel") == 2))) {
                $arResumoCorrente[$inCountResumoCorrente]["indentacao"] = 1;
                $arResumoCorrente[$inCountResumoCorrente]["descricao"]  = $rsRecordSet->getCampo("descricao");
                $arResumoCorrente[$inCountResumoCorrente]["valor"]      = $inValor;
                $inSomaCorrentes = $inSomaCorrentes + $rsRecordSet->getCampo('valor');
                $inCountResumoCorrente++;
            }

            $arOrcamento[$inCount]['valor_d']       = '';
            $arOrcamento[$inCount]['valor_f']       = $inValor;
            $arOrcamento[$inCount]['valor_e']       = '';
         } else {
            if ($stGrupo == 9) {
                $arResumoCorrente[$inCountResumoCorrente]["indentacao"] = 1;
                $arResumoCorrente[$inCountResumoCorrente]["descricao"]  = $rsRecordSet->getCampo("descricao");
                $arResumoCorrente[$inCountResumoCorrente]["valor"]      = $inValor;
                $inSomaCorrentes = $inSomaCorrentes + $rsRecordSet->getCampo('valor');
                $inCountResumoCorrente++;
            }
            $arOrcamento[$inCount]['valor_d']       = '';
            $arOrcamento[$inCount]['valor_f']       = '';
            $arOrcamento[$inCount]['valor_e']       = $inValor;
         }
         $inCount++;
         $rsRecordSet->proximo();
    }

    $arResumoCorrente[$inCountResumoCorrente]["valor"] = "______________________";
    $inCountResumoCorrente++;
    $arResumoCapital[$inCountResumoCapital]["valor"]   = "______________________";
    $inCountResumoCapital++;

    $arResumoCorrente[$inCountResumoCorrente]["indentacao"] = 2;
    $arResumoCorrente[$inCountResumoCorrente]["descricao"]  = "Total RECEITAS CORRENTES";
    $arResumoCorrente[$inCountResumoCorrente]["valor"]      = number_format($inSomaCorrentes,2,",",".");

    $arResumoCapital[$inCountResumoCapital]["indentacao"]   = 2;
    $arResumoCapital[$inCountResumoCapital]["descricao"]    = "Total RECEITAS DE CAPITAL";
    $arResumoCapital[$inCountResumoCapital]["valor"]        = number_format($inSomaCapital,2,",",".");

    $arNovo = array_merge ($arResumoCorrente, $arResumoCapital);
    $inIndice = count ($arNovo);
    $arNovo[$inIndice]["valor"] = "__________________________";
    $inIndice++;

        $arNovo[$inIndice]["indentacao"] = 3;
    $arNovo[$inIndice]["descricao"] = "T o t a l   G e r a l...";
    $arNovo[$inIndice]["valor"] = number_format(($inSomaCapital + $inSomaCorrentes),2,",",".");

    $inIndice++;
    $arNovo[$inIndice]["indentacao"]    = 1;
    $arNovo[$inIndice]["descricao"]     = "";

    $inIndice++;
    $arNovo[$inIndice]["indentacao"]    = 1;
    $arNovo[$inIndice]["descricao"]     = "ENTIDADES RELACIONADAS";

    $inEntidades = str_replace("'","",$this->getEntidades() );
    $arEntidades = explode(",",$inEntidades );

    foreach ($arEntidades as $key => $inCodEntidade) {
        $inIndice++;
        $this->obREntidade->setCodigoEntidade( $inCodEntidade );
        $this->obREntidade->consultarNomes($rsLista);
        $arNovo[$inIndice]["indentacao"]    = 1;
        $arNovo[$inIndice]["descricao"]     = "- ".$rsLista->getCampo("entidade");
    }

    $inIndice++;
    $arNovo[$inIndice]["indentacao"]    = 1;
    $arNovo[$inIndice]["descricao"]     = "";

    $rsRecordSetNovo  = new RecordSet;
    $rsRecordSet2Novo = new RecordSet;

    if($arOrcamento)
       $rsRecordSetNovo->preenche  ( $arOrcamento );
    if($arNovo)
        $rsRecordSet2Novo->preenche ( $arNovo      );

    $rsRecordSet  = $rsRecordSetNovo;
    $rsRecordSet2 = $rsRecordSet2Novo;

    return $obErro;

}
}
