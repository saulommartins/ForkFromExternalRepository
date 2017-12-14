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
    * Classe de mapeamento da tabela compras.ordem_anulacao
    * Data de Criação: 30/06/2006

    * @author Analista: Diego Victoria
    * @author Desenvolvedor: Leandro André Zis

    * @package URBEM
    * @subpackage Mapeamento

    $Id: TComprasOrdemAnulacao.class.php 59612 2014-09-02 12:00:51Z gelson $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  compras.ordem_anulacao
  * Data de Criação: 30/06/2006

  * @author Analista: Diego Victoria
  * @author Desenvolvedor: Leandro André Zis

  * @package URBEM
  * @subpackage Mapeamento
*/
class TComprasOrdemAnulacao extends Persistente
{
    /**
        * Método Construtor
        * @access Public
    */
    public function TComprasOrdemAnulacao()
    {
        parent::Persistente();
        $this->setTabela("compras.ordem_anulacao");

        $this->setCampoCod('');
        $this->setComplementoChave('exercicio, cod_entidade, cod_ordem, timestamp, tipo');

        $this->AddCampo('exercicio'   , 'char'     , true , '4'  , true , true );
        $this->AddCampo('cod_entidade', 'integer'  , true , ''   , true , true );
        $this->AddCampo('cod_ordem'   , 'integer'  , true , ''   , true , true );
        $this->AddCampo('motivo'      , 'char'     , false, '200', false, false);
        $this->AddCampo('tipo'        , 'char'     , true , '1'  , true , true );

    }

    /**
        * @access Public
        * @param  Object  $rsRecordSet Objeto RecordSet
        * @param  String  $stOrdem     String de Ordenação do SQL (ORDER BY)
        * @param  Boolean $boTransacao
        * @return Object  Objeto Erro
        * @author Henrique Girardi dos Santos
        * @date 01/11/2007
    */
    public function recuperaDados(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        return $this->executaRecupera("montaRecuperaDados",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    /**
        * Monta o sql onde retorna todos os dados da tabela, inclusive o timestamp
        * @return string $stSql
        * @author Henrique Girardi dos Santos
        * @date 01/11/2007
    */
    public function montaRecuperaDados()
    {
        $stSql  = "
                    SELECT exercicio
                        , cod_entidade
                        , cod_ordem
                        , timestamp
                        , motivo
                    FROM compras.ordem_anulacao
                    WHERE";

        if ($this->getDado("exercicio")) {
            $stSql .= " ordem_anulacao.exercicio = '".$this->getDado("exercicio")."' \nAND ";
        }

        if ($this->getDado("cod_entidade")) {
            $stSql .= " ordem_anulacao.cod_entidade = ".$this->getDado("cod_entidade")." \nAND ";
        }

        if ($this->getDado("cod_ordem")) {
            $stSql .= " ordem_anulacao.cod_ordem = ".$this->getDado("cod_ordem")." \nAND ";
        }

        if ($this->getDado("tipo")) {
            $stSql .= " ordem_anulacao.tipo = '".$this->getDado("tipo")."' \nAND ";
        }

        $stSql = substr($stSql, 0, strlen($stSql)-5);

        return $stSql;
    }
}
?>
