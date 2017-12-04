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
    * Classe de Regra de Negócio Metas de Execução da Despesa
    * Data de Criação   : 14/07/2004

    * @author Analista: Jorge B. Ribarr
    * @author Desenvolvedor: Roberto Pawelski Rodrigues

    * @package URBEM
    * @subpackage Regra

    $Revision: 30824 $
    $Name$
    $Author: lbbarreiro $
    $Date: 2008-04-07 10:06:52 -0300 (Seg, 07 Abr 2008) $

    * Casos de uso: uc-02.01.06
*/

/*
$Log$
Revision 1.11  2007/07/30 15:10:25  leandro.zis
Correção para PHP5

Revision 1.10  2007/02/28 13:26:10  luciano
#7317#

Revision 1.9  2007/01/30 11:39:38  luciano
#7317#

Revision 1.8  2006/07/05 20:42:11  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_FW_BANCO_DADOS."Transacao.class.php"     );
include_once ( CAM_GF_ORC_NEGOCIO."ROrcamentoPrevisaoOrcamentaria.class.php"  );
include_once ( CAM_GF_ORC_NEGOCIO."ROrcamentoConfiguracao.class.php" );
include_once ( CAM_GF_ORC_NEGOCIO."ROrcamentoDespesa.class.php" );

/**
* Classe de Regra de Negócio Metas de Execução da Despesa
* Data de Criação   : 14/07/2004
* @author Analista: Jorge B. Ribarr
* @author Desenvolvedor: Roberto Pawelski Rodrigues
*/
class ROrcamentoPrevisaoDespesa
{
    /**
    * @var Integer
    * @access Private
    */
    public $inPeriodo;

    /**
    * @var Array
    * @access Private
    */
    public $arID;

    /**
    * @var Integer
    * @access Private
    */
    public $inQtdColunas;

    /**
    * @var Integer
    * @access Private
    */
    public $inQtdLinhas;

    /**
    * @var Float
    * @access Private
    */
    public $flValorPrevisto;

    /**
    * @var Integer
    * @access Private
    */
    public $inCodigoDespesa;

    /**
    * @var Integer
    * @access Private
    */
    public $inExercicio;

    /**
    * @var Objeto
    * @access Private
    */
    public $obROrcamentoDespesa;

    /**
    * @var Objeto
    * @access Private
    */
    public $obROrcamentoPrevisaoOrcamentaria;

    /**
    * @var Objeto
    * @access Private
    */
    public $obRConfiguracaoOrcamento;

    /**
    * @var Objeto
    * @access Private
    */
    public $obTransacao;

    /**
    * @var Objeto
    * @access Private
    */
    public $stDescricao;

    /**
    * @var Objeto
    * @access Private
    */
    public $inCodDotacaoInicial;

    /**
    * @var Objeto
    * @access Private
    */
    public $inCodDotacaoFinal;

    /**
    * @var Objeto
    * @access Private
    */
    public $stCodRubricaDespesaInicial;

    /**
    * @var Objeto
    * @access Private
    */
    public $stCodRubricaDespesaFinal;

    /**
    * @access Public
    * @param Object $valor
    */
    public function setROrcamentoDespesa($valor) { $this->obROrcamentoDespesa       = $valor; }

    /**
    * @access Public
    * @param Integer $valor
    */
    public function setCodigoDespesa($valor) { $this->inCodigoDespesa  = $valor; }

    /**
    * @access Public
    * @param Integer $valor
    */
    public function setID($valor) { $this->arID             = $valor; }

    /**
    * @access Public
    * @param Integer $valor
    */
    public function setQtdColunas($valor) { $this->inQtdColunas     = $valor; }

    /**
    * @access Public
    * @param Integer $valor
    */
    public function setQtdLinhas($valor) { $this->inQtdLinhas      = $valor; }

    /**
    * @access Public
    * @param Integer $valor
    */
    public function setPeriodo($valor) { $this->inPeriodo        = $valor; }

    /**
    * @access Public
    * @param Integer $valor
    */
    public function setValorPrevisto($valor) { $this->flValorPrevisao  = $valor; }

    /**
    * @access Public
    * @param Integer $valor
    */
    public function setExercicio($valor) { $this->inExercicio      = $valor; }

    /**
    * @access Public
    * @param Object $valor
    */
    public function setROrcamentoPrevisaoOrcamentaria($valor) { $this->obROrcamentoPrevisaoOrcamentaria = $valor; }

    /**
    * @access Public
    * @param Object $valor
    */
    public function setRConfiguracaoOrcamento($valor) { $this->obRConfiguracaoOrcamento = $valor; }

    /**
    * @access Public
    * @param Object $valor
    */
    public function setTransacao($valor) { $this->obTransacao = $valor; }

    /**
    * @access Public
    * @param Object $valor
    */
    public function setDescricao($valor) { $this->stDescricao = $valor; }

    /**
    * @access Public
    * @param Object $valor
    */
    public function setCodDotacaoInicial($valor) { $this->inCodDotacaoInicial = $valor; }

     /**
    * @access Public
    * @param Object $valor
    */
    public function setCodDotacaoFinal($valor) { $this->inCodDotacaoFinal = $valor; }

    /**
    * @access Public
    * @param Object $valor
    */
    public function setCodRubricaDespesaInicial($valor) { $this->stCodRubricaDespesaInicial = $valor; }

    /**
    * @access Public
    * @param Object $valor
    */
    public function setCodRubricaDespesaFinal($valor) { $this->stCodRubricaDespesaFinal = $valor; }

    /**
    * @access Public
    * @return Object
    */
    public function getROrcamentoDespesa() { return $this->obROrcamentoDespesa;      }

    /**
    * @access Public
    * @return Integer
    */
    public function getCodigoDespesa() { return $this->inCodigoDespesa; }

    /**
    * @access Public
    * @return Integer
    */
    public function getID() { return $this->arID;             }

    /**
    * @access Public
    * @return Integer
    */
    public function getQtdColunas() { return $this->inQtdColunas;     }

    /**
    * @access Public
    * @return Integer
    */
    public function getQtdLinhas() { return $this->inQtdLinhas;      }

    /**
    * @access Public
    * @return Integer
    */
    public function getPeriodo() { return $this->inPeriodo;       }

    /**
    * @access Public
    * @return Integer
    */
    public function getExercicio() { return $this->inExercicio;     }

    /**
    * @access Public
    * @return Float
    */
    public function getValorPrevisto() { return $this->flValorPrevisao; }

    /**
    * @access Public
    * @return Object
    */
    public function getROrcamentoPrevisaoOrcamentaria() { return $this->obROrcamentoPrevisaoOrcamentaria; }

    /**
    * @access Public
    * @return Object
    */
    public function getRConfiguracaoOrcamento() { return $this->obRConfiguracaoOrcamento; }

    /**
    * @access Public
    * @return Object
    */
    public function getTransacao() { return $this->obTransacao; }

    /**
    * @access Public
    * @return Object
    */
    public function getDescricao() { return $this->stDescricao; }

    /**
    * @access Public
    * @return Object
    */
    public function getCodDotacaoInicial() { return $this->inCodDotacaoInicial; }

    /**
    * @access Public
    * @return Object
    */
    public function getCodDotacaoFinal() { return $this->inCodDotacaoFinal; }

    /**
    * @access Public
    * @return Object
    */
    public function getCodRubricaDespesaInicial() { return $this->stCodRubricaDespesaInicial; }

    /**
    * @access Public
    * @return Object
    */
    public function getCodRubricaDespesaFinal() { return $this->stCodRubricaDespesaFinal; }

    /**
    * Método Construtor
    * @access Private
    */
    public function ROrcamentoPrevisaoDespesa()
    {
        $this->setTransacao                     ( new Transacao                     );
        $this->setROrcamentoDespesa             ( new ROrcamentoDespesa             );
        $this->setRConfiguracaoOrcamento        ( new ROrcamentoConfiguracao        );
        $this->setROrcamentoPrevisaoOrcamentaria( new ROrcamentoPrevisaoOrcamentaria);
        $this->setExercicio                     ( Sessao::getExercicio()                );
    }

    /**
    * Cadastra e/ou Altera Metas de Execução da Despesa
    * @access Public
    * @param  Object $boTransacao Parâmetro Transação, caso esta exista
    * @return Object Objeto Erro retorna o valor, validando o método
    */
    public function salvar($boTransacao = "")
    {
        include_once ( CAM_GF_ORC_MAPEAMENTO."TOrcamentoPrevisaoDespesa.class.php" );
        $obTOrcamentoPrevisaoDespesa      = new TOrcamentoPrevisaoDespesa;

        $obTOrcamentoPrevisaoDespesa->setDado( "exercicio"   , $this->getExercicio()     );
        $obTOrcamentoPrevisaoDespesa->setDado( "periodo"     , $this->getPeriodo()       );
        $obTOrcamentoPrevisaoDespesa->setDado( "cod_despesa" , $this->getCodigoDespesa() );
        $obTOrcamentoPrevisaoDespesa->setDado( "vl_previsto" , $this->getValorPrevisto() );
        $obErro = $obTOrcamentoPrevisaoDespesa->inclusao( $boTransacao );
            
        return $obErro;
    }

    /**
    * Executa um recuperaTodos na classe Persistente Metas de Execução da Despesa
    * @access Public
    * @param  Object $rsListaCategoria Retorna o RecordSet preenchido
    * @param  String $stOrdem Parâmetro de Ordenação
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
    */
    public function listar(&$rsLista, $stFiltro = "", $boTransacao = "")
    {
        include_once ( CAM_GF_ORC_MAPEAMENTO."TOrcamentoPrevisaoDespesa.class.php" );
        $obTOrcamentoPrevisaoDespesa      = new TOrcamentoPrevisaoDespesa;

        $stFiltro = " ";
        if ( $this->getDescricao() ) {
            $stFiltro .= " AND CR.descricao ilike '%".$this->getDescricao()."%'";
        }

        if ( $this->getCodDotacaoInicial() && $this->getCodDotacaoFinal() ) {
            $stFiltro .= " AND O.cod_despesa BETWEEN '".$this->getCodDotacaoInicial()."' AND '".$this->getCodDotacaoFinal()."'";
        }

        if ( $this->getCodDotacaoInicial() && !$this->getCodDotacaoFinal() ) {
            $stFiltro .= " AND O.cod_despesa = ".$this->getCodDotacaoInicial();
        }

        if ( !$this->getCodDotacaoInicial() && $this->getCodDotacaoFinal() ) {
            $stFiltro .= " AND O.cod_despesa = ".$this->getCodDotacaoFinal();
        }

        if ( $this->getCodRubricaDespesaInicial() && $this->getCodRubricaDespesaFinal() ) {
            $stFiltro .= " AND CR.mascara_classificacao BETWEEN '".$this->getCodRubricaDespesaInicial()."' AND '".$this->getCodRubricaDespesaFinal()."'";
        }

        if ( $this->getExercicio() ) {
            $stFiltro .= " AND O.exercicio = '" . $this->getExercicio() . "' ";
        }

        if ( $this->obROrcamentoDespesa->obROrcamentoEntidade->obRCGM->getNumCGM() ) {
            $stFiltro .= " AND UE.numcgm = ".$this->obROrcamentoDespesa->obROrcamentoEntidade->obRCGM->getNumCGM();
        }
        if ( $this->obROrcamentoDespesa->obROrcamentoEntidade->getCodigoEntidade() ) {
            $stFiltro .= " AND O.cod_entidade = ".$this->obROrcamentoDespesa->obROrcamentoEntidade->getCodigoEntidade();
        }
        if ( $this->obROrcamentoDespesa->obROrcamentoUnidadeOrcamentaria->obROrcamentoOrgaoOrcamentario->getNumeroOrgao() ) {
            $stFiltro .= " AND O.num_orgao = ".$this->obROrcamentoDespesa->obROrcamentoUnidadeOrcamentaria->obROrcamentoOrgaoOrcamentario->getNumeroOrgao();
        }
        if ( $this->obROrcamentoDespesa->obROrcamentoUnidadeOrcamentaria->getNumeroUnidade() ) {
            $stFiltro .= " AND O.num_unidade = ".$this->obROrcamentoDespesa->obROrcamentoUnidadeOrcamentaria->getNumeroUnidade();
        }

        $stOrdem = " cod_despesa ";
        $obErro = $obTOrcamentoPrevisaoDespesa->recuperaRelacionamento( $rsLista, $stFiltro ,$stOrdem ,$boTransacao );

        return $obErro;
    }

    /**
        * Executa um recuperaTodos na classe Persistente Previsão Despesa
        * @access Public
        * @param  Object $rsListaCategoria Retorna o RecordSet preenchido
        * @param  String $stOrdem Parâmetro de Ordenação
        * @param  Object $boTransacao Parâmetro Transação
        * @return Object Objeto Erro
    */
    public function listarPeriodo(&$rsLista, $stFiltro = "", $boTransacao = "")
    {
        include_once ( CAM_GF_ORC_MAPEAMENTO."TOrcamentoPrevisaoDespesa.class.php" );
        $obTOrcamentoPrevisaoDespesa      = new TOrcamentoPrevisaoDespesa;

        $stFiltro;
        if ($stFiltro !="") {
            $stFiltro .= "  AND  ";
        }
        if ( $this->getExercicio() ) {
            $stFiltro .= " exercicio = '".$this->getExercicio()."' AND ";
        }
        if ($stFiltro) {
            $stFiltro = " WHERE ".substr( $stFiltro, 0, strlen( $stFiltro ) - 4 );
        }
        $stOrdem = " ORDER BY cod_despesa, periodo ";
        $obErro = $obTOrcamentoPrevisaoDespesa->recuperaTodos( $rsLista, $stFiltro, $stOrdem, $obTransacao );

        return $obErro;
    }

    /**
        * Limpa os dados das Metas de Execução da Despesa
        * @access Public
        * @param  Object $boTransacao Parâmetro Transação
        * @return Object Objeto Erro
    */
    public function limparDados($boTransacao)
    {
        include_once ( CAM_GF_ORC_MAPEAMENTO."TOrcamentoPrevisaoDespesa.class.php" );
        $obTOrcamentoPrevisaoDespesa      = new TOrcamentoPrevisaoDespesa;

            $obTOrcamentoPrevisaoDespesa->setDado( "exercicio"   , $this->getExercicio()     );
            $obTOrcamentoPrevisaoDespesa->setDado( "cod_despesa" , $this->getCodigoDespesa() );
            $obErro = $obTOrcamentoPrevisaoDespesa->recuperaLimpaDespesa( $rsLista , "", "", $boTransacao );
//        }
        return $obErro;
    }

}
