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
    * Mapeamento da tabela ldo.tipo_divida
    * Data de Criação   : 08/07/2009

    * @author Analista      Tonismar Régis Bernardo
    * @author Desenvolvedor Eduardo Paculski Schitz

    * @package URBEM
    * @subpackage

    * @ignore

    $Id:$
 */

class TLDOTipoDivida extends Persistente
{
    /**
     * Método construtor
     * @access private
     */
    public function __construct()
    {
        parent::Persistente();

        $this->setTabela('ldo.tipo_divida');

        $this->setCampoCod('cod_tipo');

        $this->addCampo('cod_tipo' , 'integer', true, ''   , true , false);
        $this->addCampo('descricao', 'varchar', true, '150', false, false);

    }

    /**
     * Método que retorna as dívidas para o LDO
     *
     * @author      Analista        Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
     * @author      Desenvolvedor   Eduardo Schitz      <eduardo.schitz@cnm.org.br>
     * @param object  $rsRecordSet
     * @param string  $stFiltro    Filtros alternativos que podem ser passados
     * @param string  $stOrder     Ordenacao do SQL
     * @param boolean $boTransacao Usar transacao
     *
     * @return object $rsRecordSet
     */
    public function listDividasLDO(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        $stSql = "
             SELECT ordem
                  , cod_tipo
                  , especificacao
                  , bo_orcamento_1
                  , bo_orcamento_2
                  , bo_orcamento_3
                  , bo_orcamento_4
                  , bo_orcamento_5
                  , bo_orcamento_6
                  , valor_1
                  , valor_2
                  , valor_3
                  , valor_4
                  , valor_5
                  , valor_6
                  , exercicio_1
                  , exercicio_2
                  , exercicio_3
                  , exercicio_4
                  , exercicio_5
                  , exercicio_6
               FROM ldo.evolucao_divida(".$this->getDado('cod_ppa').", '".$this->getDado('ano')."') AS (
                              ordem             INTEGER
                            , cod_tipo          INTEGER
                            , especificacao     VARCHAR
                            , valor_1           DECIMAL(14,2)
                            , valor_2           DECIMAL(14,2)
                            , valor_3           DECIMAL(14,2)
                            , valor_4           DECIMAL(14,2)
                            , valor_5           DECIMAL(14,2)
                            , valor_6           DECIMAL(14,2)
                            , bo_orcamento_1    DECIMAL(1)
                            , bo_orcamento_2    DECIMAL(1)
                            , bo_orcamento_3    DECIMAL(1)
                            , bo_orcamento_4    DECIMAL(1)
                            , bo_orcamento_5    DECIMAL(1)
                            , bo_orcamento_6    DECIMAL(1)
                            , exercicio_1       CHAR(4)
                            , exercicio_2       CHAR(4)
                            , exercicio_3       CHAR(4)
                            , exercicio_4       CHAR(4)
                            , exercicio_5       CHAR(4)
                            , exercicio_6       CHAR(4)
                    ) ORDER BY ordem
        ";

        return $this->executaRecuperaSql($stSql,$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    /**
     * Método que retorna os serviços das dívidas para o LDO
     *
     * @author      Analista        Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
     * @author      Desenvolvedor   Eduardo Schitz      <eduardo.schitz@cnm.org.br>
     * @param object  $rsRecordSet
     * @param string  $stFiltro    Filtros alternativos que podem ser passados
     * @param string  $stOrder     Ordenacao do SQL
     * @param boolean $boTransacao Usar transacao
     *
     * @return object $rsRecordSet
     */
    public function listServicosLDO(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        $stSql = "
             SELECT ordem
                  , cod_tipo
                  , especificacao
                  , bo_orcamento_1
                  , bo_orcamento_2
                  , bo_orcamento_3
                  , bo_orcamento_4
                  , bo_orcamento_5
                  , bo_orcamento_6
                  , valor_1
                  , valor_2
                  , valor_3
                  , valor_4
                  , valor_5
                  , valor_6
                  , exercicio_1
                  , exercicio_2
                  , exercicio_3
                  , exercicio_4
                  , exercicio_5
                  , exercicio_6
               FROM ldo.servico_divida(".$this->getDado('cod_ppa').", '".$this->getDado('ano')."', ".$this->getDado('cod_selic').") AS (
                              ordem             INTEGER
                            , cod_tipo          INTEGER
                            , especificacao     VARCHAR
                            , valor_1           DECIMAL(14,2)
                            , valor_2           DECIMAL(14,2)
                            , valor_3           DECIMAL(14,2)
                            , valor_4           DECIMAL(14,2)
                            , valor_5           DECIMAL(14,2)
                            , valor_6           DECIMAL(14,2)
                            , bo_orcamento_1    DECIMAL(1)
                            , bo_orcamento_2    DECIMAL(1)
                            , bo_orcamento_3    DECIMAL(1)
                            , bo_orcamento_4    DECIMAL(1)
                            , bo_orcamento_5    DECIMAL(1)
                            , bo_orcamento_6    DECIMAL(1)
                            , exercicio_1       CHAR(4)
                            , exercicio_2       CHAR(4)
                            , exercicio_3       CHAR(4)
                            , exercicio_4       CHAR(4)
                            , exercicio_5       CHAR(4)
                            , exercicio_6       CHAR(4)
                    ) ORDER BY ordem
        ";

        return $this->executaRecuperaSql($stSql,$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

}

?>
