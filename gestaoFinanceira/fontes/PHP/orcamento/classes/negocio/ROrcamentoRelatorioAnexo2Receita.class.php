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
    * Data de CriaÃ§Ã£o   : 23/09/2004

    * @author Desenvolvedor: Eduardo Martins
    * @author Desenvolvedor: Gustavo Tourinho

    * @ignore

    $Revision: 32085 $
    $Name$
    $Author: rodrigosoares $
    $Date: 2008-01-07 16:38:09 -0200 (Seg, 07 Jan 2008) $

    * Casos de uso: uc-02.01.10
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CLA_PERSISTENTE_RELATORIO    );
include_once( CAM_GF_ORC_NEGOCIO."ROrcamentoEntidade.class.php"              );
include_once( CAM_GF_ORC_MAPEAMENTO."FOrcamentoSomatorioReceita.class.php" );

class ROrcamentoRelatorioAnexo2Receita extends PersistenteRelatorio
{
/**
    * @var Object
    * @access Private
*/
var $obFOrcamentoSomatorioReceita;
var $stFiltro;
var $inExercicio;
var $obREntidade;

/**
     * @access Public
     * @param Object $valor
*/
function setFOrcamentoSomatorioReceita($valor) { $this->obFOrcamentoSomatorioReceita  = $valor; }
function setFiltro($valor) { $this->stFiltro                      = $valor; }
function setExercicio($valor) { $this->inExercicio                   = $valor; }
function setREntidade($valor) { $this->obREntidade                   = $valor; }

/**
     * @access Public
     * @param Object $valor
*/
function getFOrcamentoSomatorioReceita() { return $this->obFOrcamentoSomatorioReceita; }
function getFiltro() { return $this->stFiltro                    ; }
function getExercicio() { return $this->inExercicio                 ; }
function getREntidade() { return $this->obREntidade                 ; }

/**
    * Método Construtor
    * @access Private
*/
function ROrcamentoRelatorioAnexo2Receita()
{
    $this->setFOrcamentoSomatorioReceita ( new FOrcamentoSomatorioReceita   );
    $this->obREntidade                  = new ROrcamentoEntidade;
    $this->obREntidade->obRCGM->setNumCGM( Sessao::read('numCgm')         );
}

/**
    * Método abstrato
    * @access Public
*/
function geraRecordSet(&$rsRecordSet , &$rsRecordSet2, $stOrder = "")
{
    $this->obFOrcamentoSomatorioReceita->setDado ("exercicio", $this->inExercicio);
    $this->obFOrcamentoSomatorioReceita->setDado ("stFiltro", $this->stFiltro);

    $stEntidade = $this->stFiltro;

    $stFiltro = " WHERE valor <> 0";

    $obErro = $this->obFOrcamentoSomatorioReceita->recuperaTodos( $rsRecordSet, $stFiltro, "" );

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

    $stEntidade = substr(trim($stEntidade),strpos($stEntidade,"("));
    $stEntidade = substr($stEntidade,0,strlen($stEntidade)-1);

    $inEntidades = str_replace("'","",$stEntidade);
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
    $rsRecordSetNovo->preenche  ( $arOrcamento );
    $rsRecordSet2Novo->preenche ( $arNovo      );

    $rsRecordSet  = $rsRecordSetNovo;
    $rsRecordSet2 = $rsRecordSet2Novo;

    return $obErro;
}
}
