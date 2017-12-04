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
    * Classe de Regra de Negócio Pessoal CID
    * Data de Criação   : 20/12/2004

    * @author Analista: Leandro Oliveira.
    * @author Desenvolvedor: Rafael Almeida

    * @package URBEM
    * @subpackage Regra

      $Revision: 30566 $
      $Name$
      $Author: souzadl $
      $Date: 2007-06-07 09:41:04 -0300 (Qui, 07 Jun 2007) $

      Caso de uso: uc-04.04.07

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GRH_PES_MAPEAMENTO."TPessoalCID.class.php"  );

/**
    * Classe de Regra de Negócio Pessoal CID
    * Data de Criação   : 20/12/2004

    * @author Analista: Leandro Oliveira.
    * @author Desenvolvedor: Rafael Almeida

    * @package URBEM
    * @subpackage Regra
*/
class RPessoalCID
{
    /**
    * @var Objeto
    * @access Private
    */
    public $obTPessoalCID;

    /**
    * @var Objeto
    * @access Private
    */
    public $obTransacao;

    /**
    * @var Integer
    * @access Private
    */
    public $inCodCID;

    /**
    * @var Integer
    * @access Private
    */
    public $inCodTipoDeficiencia;

    /**
    * @var String
    * @access Private
    */
    public $stSigla;

    /**
    * @var String
    * @access Private
    */
    public $stDescricao;
    /**
    * @var Object
    * @access Private
    */
    public $roRFolhaPagamentoIRRF;

    public function setCodCID($valor) { $this->inCodCID    = $valor; }
    public function setCodTipoDeficiencia($valor) { $this->inCodTipoDeficiencia    = $valor; }
    /**
    * @access Public
    * @param Object $valor
    */
    public function setSigla($valor) { $this->stSigla     = $valor; }
    /**
    * @access Public
    * @param Object $valor
    */
    public function setDescricao($valor) { $this->stDescricao = $valor; }
    /**
    * @access Public
    * @param Object $valor
    */
    public function setTPessoalCID($valor) { $this->obTPessoalCID = $valor; }
    /**
         * @access Public
         * @param Object $valor
    */
    public function setTransacao($valor) { $this->obTransacao                 = $valor; }
    /**
         * @access Public
         * @param Object $valor
    */
    public function setRORFolhaPagamentoIRRF(&$valor) { $this->roRFolhaPagamentoIRRF      = &$valor; }

    /**
        * @access Public
        * @return Object
    */
    public function getTPessoalCID() { return $this->obTPessoalCID; }
    /**
        * @access Public
        * @return Integer
    */
    public function getCodCID() { return $this->inCodCID;      }
    /**
        * @access Public
        * @return Integer
    */
    public function getCodTipoDeficiencia() { return $this->inCodTipoDeficiencia;      }
    /**
        * @access Public
        * @return String
    */
    public function getSigla() { return $this->stSigla;       }
    /**
        * @access Public
        * @return String
    */
    public function getDescricao() { return $this->stDescricao;   }
    /**
        * @access Public
        * @return Object
    */
    public function getTransacao() { return $this->obTransacao;                 }

    /**
         * @access Public
         * @param Object $valor
    */
    public function getRORFolhaPagamentoIRRF() { return $this->roRFolhaPagamentoIRRF;     }

    /**
    * Método Construtor
    * @access Private
    */
    public function RPessoalCID()
    {
        $this->setTPessoalCID     ( new TPessoalCID     );
        $this->setTransacao       ( new Transacao       );
    }

    public function incluir($boTransacao = "")
    {
        $boFlagTransacao = false;
        $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
        if ( !$obErro->ocorreu() ) {
            $obErro = $this->obTPessoalCID->proximoCod( $this->inCodCID, $boTransacao );
            if ( !$obErro->ocorreu() ) {
                $this->obTPessoalCID->setDado("cod_cid"  , $this->getCodCID()    );
                $this->obTPessoalCID->setDado("sigla"    , $this->getSigla()     );
                $this->obTPessoalCID->setDado("descricao", $this->getDescricao() );
                $this->obTPessoalCID->setDado("cod_tipo_deficiencia", $this->getCodTipoDeficiencia() );
                $obErro = $this->obTPessoalCID->inclusao( $boTransacao );
            }
        }
        $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTPessoalCID );

        return $obErro;
    }

    public function alterar($boTransacao = "")
    {
        $boFlagTransacao = false;
        $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
        if ( !$obErro->ocorreu() ) {
            if ( !$obErro->ocorreu() ) {
                $this->obTPessoalCID->setDado("cod_cid"  , $this->getCodCID()    );
                $this->obTPessoalCID->setDado("sigla"    , $this->getSigla()     );
                $this->obTPessoalCID->setDado("descricao", $this->getDescricao() );
                $this->obTPessoalCID->setDado("cod_tipo_deficiencia", $this->getCodTipoDeficiencia() );
                $obErro = $this->obTPessoalCID->alteracao( $boTransacao );
            }
        }
        $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTPessoalCID );

        return $obErro;
    }

    public function excluir($boTransacao = "")
    {
        $boErro = false;

        $boFlagTransacao = false;
        $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
        if ( !$obErro->ocorreu() ) {

            //procurando ligações do CID com as tabelas: pessoal.servidor_cid,
            //pessoal.dependente_cid, folhapagamento.tabela_irrf_cid, se tiver não pode excluir
            $obRDados = new Recordset;

            include_once ( CAM_GRH_PES_MAPEAMENTO. "TPessoalServidorCid.class.php");
            $obServidorCID = new TPessoalServidorCid;
            $obErro =  $obServidorCID->recuperaCid ( $obRDados, " where servidor_cid.cod_cid = ". $this->getCodCID() );
            $boErro = ( $obRDados->getNumLinhas () > 0 );

            if ( ( !$boErro) and ( !$obErro->ocorreu() ) ) {
                // tabela  pessoal.dependente_cid
                include_once ( CAM_GRH_PES_MAPEAMENTO. "TPessoalDependenteCid.class.php");
                $obDependenteCID = new TPessoalDependenteCid;
                $obErro =  $obDependenteCID->recuperaTodos ( $obRDados, " where dependente_cid.cod_cid = ". $this->getCodCID() );
                $boErro = ( $obRDados->getNumLinhas () > 0 );
            }

            if ( ( !$boErro ) and ( !$obErro->ocorreu() ) ) {
                // Tabela folhapagamento.tabela_irrf_cid
                include_once ( CAM_GRH_FOL_MAPEAMENTO. "TFolhaPagamentoTabelaIrrfCid.class.php" );
                $obTabelaIrrrfCid = new TFolhaPagamentoTabelaIrrfCid;
                $obErro =  $obTabelaIrrrfCid->recuperaTodos ( $obRDados, " where tabela_irrf_cid.cod_cid = ". $this->getCodCID());
                $boErro = ( $obRDados->getNumLinhas () > 0 );
            }

            if ( ( $boErro ) and ( !$obErro->ocorreu() ) ) {
                $obErro->setDescricao ("O CID selecionado não pode ser excluído,
                                        o mesmo está sendo utilizado em outro cadastro.");
            } else {
                $this->obTPessoalCID->setDado("cod_cid", $this->getCodCID() );
                $obErro = $this->obTPessoalCID->exclusao( $boTransacao );
            }
        }

        $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTBeneficioFornecedor );

        return $obErro;
    }
    /**
    * Executa um recuperaTodos na classe Persistente PessoalServidorCID
    * @access Public
    * @param  Object $rsListaCategoria Retorna o RecordSet preenchido
    * @param  String $stOrdem Parâmetro de Ordenação
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
    */
    public function listar(&$rsCID, $stFiltro = "", $stOrder = "", $boTransacao = "")
    {
        $obErro = $this->obTPessoalCID->recuperaTodos( $rsCID , $stFiltro ,$stOrder ,$boTransacao );

        return $obErro;
    }

    /**
    * Executa um recuperaTodos na classe Persistente PessoalServidorCID
    * @access Public
    * @param  Object $rsListaCategoria Retorna o RecordSet preenchido
    * @param  String $stOrdem Parâmetro de Ordenação
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
    */
    public function listarOrdenadoPorDescricao(&$rsCID, $boTransacao = "")
    {
        $stOrder = " descricao";
        if ( $this->getCodCID() ) {
            $stFiltro .= " AND cod_cid = ".$this->getCodCID();
        }
        if ($stFiltro != "") {
            $stFiltro = " WHERE ".substr($stFiltro,4,strlen($stFiltro));
        }
        $obErro = $this->listar( $rsCID , $stFiltro ,$stOrder ,$boTransacao );

        return $obErro;
    }

    public function listarCID(&$rsCID, $boTransacao = "")
    {
        $stFiltro    = " WHERE cod_cid > 0 \n";
        if ( $this->getSigla() ) {
            $stFiltro    .= " AND sigla like '".$this->getSigla()."%' \n";
        }
        if ( $this->getDescricao() ) {
            $stFiltro    .= " AND descricao like '".$this->getDescricao()."%' \n";
        }
        $obErro = $this->obTPessoalCID->recuperaTodos( $rsCID , $stFiltro , "sigla" ,$boTransacao );

        return $obErro;
    }

}
