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
  * Página de Regra de Lancamento de Receita
  * Data de criação : 17/06/2005

  * @author Analista: Fábio Bertoldi
  * @author Programador: tonismar R. Bernardo

  * @package URBEM
  * @subpackage Regras

    * $Id: RARRAvaliacaoEconomica.class.php 59612 2014-09-02 12:00:51Z gelson $

  Caso de uso: uc-05.03.06
**/

/*
$Log$
Revision 1.6  2006/10/26 14:09:49  cercato
alterando funcao avaliarCadastroEconomico retirando campos que nao existem mais no BD.

Revision 1.5  2006/09/15 11:50:14  fabio
corrigidas tags de caso de uso

Revision 1.4  2006/09/15 10:48:45  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once (CAM_GT_ARR_MAPEAMENTO."TARRCadastroEconomicoFaturamento.class.php"    );
include_once (CAM_GT_ARR_MAPEAMENTO."TARRAtributoCadEconFaturamentoValor.class.php" );
include_once (CAM_GA_ADM_NEGOCIO."RCadastroDinamico.class.php"                        );
include_once (CAM_GA_ADM_NEGOCIO."RFuncao.class.php"                                  );
include_once (CAM_GT_CEM_NEGOCIO."RCEMInscricaoEconomica.class.php"                   );

class RARRAvaliacaoEconomica
{
    /**
        * @access Private
        * @param Float
    */
    public $flFaturamento;
    /**
        * @access Private
        * @param Float
    */
    public $flComplemento;
    /**
        * @access Private
        * @param String
    */
    public $stCompetencia;
    /**
        * @access Private
        * @param Date
    */
    public $dtDataVencimento;
    /**
        * @access Private
        * @param Object
    */
    public $obRCEMInscricaoEconomica;

    /**
        * @access Public
        * @param Float Valor
    */
    public function setFaturamento($valor) { $this->flFaturamento    = $valor; }
    /**
        * @access Public
        * @param Float Valor
    */
    public function setComplemento($valor) { $this->flComplemento    = $valor; }
    /**
        * @access Public
        * @param String Valor
    */
    public function setCompetencia($valor) { $this->stCompetencia    = $valor; }
    /**
        * @access Public
        * @param Date Valor
    */
    public function setDataVencimento($valor) { $this->dtDataVencimento = $valor; }

    /**
        * @access Public
        * @return Float
    */
    public function getFaturamento() { return $this->flFaturamento;    }
    /**
        * @access Public
        * @return Float
    */
    public function getComplemento() { return $this->flComplemento;    }
    /**
        * @access Public
        * @return String
    */
    public function getCompetencia() { return $this->stCompetencia;    }
    /**
        * @access Public
        * @return Date
    */
    public function getDataVencimento() { return $this->dtDataVencimento; }

    /**
        * Metodo Construtor
        * @access Private
    */
    public function RARRAvaliacaoEconomica()
    {
        $this->obTransacao         = new Transacao;
        $this->obTARRCadastroEconomicoFaturamento = new TARRCadastroEconomicoFaturamento;
        $this->obRFuncao           = new RFuncao;
        $this->obRCEMInscricaoEconomica = new RCEMInscricaoEconomica;
        $this->obRCadastroDinamico = new RCadastroDinamico;
        $this->obRCadastroDinamico->setPersistenteValores  ( new TARRAtributoCadEconFaturamentoValor );
        $this->obRCadastroDinamico->setCodCadastro         ( 4 );
    }

    /**
        * Avaliar Cadastro Economico
        * @access Public
        * @param  Object $obTransacao Parâmetro Transação
        * @return Object Objeto Erro
    */
    public function avaliarCadastroEconomico($boTransacao = "")
    {
        $boFlagTransacao = false;
        $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
        if ( !$obErro->ocorreu() ) {
            $this->obTARRCadastroEconomicoFaturamento->setDado( "inscricao_economica"   , $this->obRCEMInscricaoEconomica->getInscricaoEconomica() );
            $this->obTARRCadastroEconomicoFaturamento->setDado( "competencia"           , $this->stCompetencia );
            $obErro = $this->obTARRCadastroEconomicoFaturamento->inclusao( $boTransacao );
            if ( !$obErro->ocorreu() ) {
                $arChaveAtributo =  array( "inscricao_economica" => $this->obRCEMInscricaoEconomica->getInscricaoEconomica() );
                $this->obRCadastroDinamico->setChavePersistenteValores( $arChaveAtributo );
                $obErro = $this->obRCadastroDinamico->salvarValores( $boTransacao );
            }
        }
        $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTARRACadastroEconomicoFaturamento );

        return $obErro;
    }

    /**
        * Lista as Inscriçoes para Avaliação de Receita
        * @access Public
        * @param  Object $rsRecordSet $obTransacao Parâmetro Transação
        * @return Object Objeto Erro
    */
    public function listarInscricaoAvaliarReceita(&$rsRecordSet, $boTransacao = "")
    {
        $stFiltro = "";
        if ( $this->obRCEMInscricaoEconomica->obRCGM->getNumCGM() ) {
            $stFiltro .= " AND CG.NUMCGM = ".$this->obRCEMInscricaoEconomica->obRCGM->getNumCGM()." ";
        }
        if ( $this->obRCEMInscricaoEconomica->getInscricaoEconomica() ) {
            $stFiltro .= " AND CE.INSCRICAO_ECONOMICA = ".$this->obRCEMInscricaoEconomica->getInscricaoEconomica()." ";
        }
        if ( is_object($this->obRCEMInscricaoEconomica->roUltimaInscricaoAtividade->roUltimaAtividade) && $this->obRCEMInscricaoEconomica->roUltimaInscricaoAtividade->roUltimaAtividade->getCodigoAtividade() ) {
            $stFiltro .= " AND AC.COD_ATIVIDADE = ".$this->obRCEMInscricaoEconomica->roUltimaInscricaoAtividade->roUltimaAtividade->getCodigoAtividade()." ";
        }
        if ( $this->obRCEMInscricaoEconomica->getDomicilioFiscal() ) {
            $stFiltro .- " AND DF.INSCRICAO_MUNICIPAL = ".$this->obRCEMInscricaoEconomica->getDomicilioFiscal()." ";
        }
        $stOrdem = " ORDER BY CE.INSCRICAO_ECONOMICA ";
        $obErro = $this->obTARRCadastroEconomicoFaturamento->recuperaAvaliarReceita( $rsRecordSet, $stFiltro, $stOrdem, $boTransacao );

        return $obErro;
    }
}
