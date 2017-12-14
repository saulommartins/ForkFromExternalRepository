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
 * Mapeamento da tabela ldo.tipo_receita_despesa
 *
 * @category    Urbem
 * @package     LDO
 * @author      Analista        Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
 * @author      Desenvolvedor   Henrique Boaventura <henrique.boaventura@cnm.org.br>
 * $Id:$
 */

class TLDOTipoReceitaDespesa extends Persistente
{
    /**
     * Método construtor
     * @access private
     */
    public function __construct()
    {
        parent::Persistente();

        $this->setTabela('ldo.tipo_receita_despesa');

        $this->setCampoCod('');
        $this->setComplementoChave('cod_tipo,tipo');

        $this->addCampo('cod_tipo'              ,'integer'  ,true, ''   ,true ,false);
        $this->addCampo('tipo'                  ,'varchar'  ,true, '1'  ,true ,false);
        $this->addCampo('cod_estrutural'        ,'varchar'  ,true, '150',false,false);
        $this->addCampo('nivel'                 ,'numeric'  ,true, '1'  ,false,false);
        $this->addCampo('descricao'             ,'varchar'  ,true, '160',false,false);
        $this->addCampo('rpps'                  ,'boolean'  ,true, ''   ,false,false);

    }

    /**
     * Método que retorna os dados para a tabela
     *
     * @author      Analista        Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
     * @author      Desenvolvedor   Henrique Boaventura <henrique.boaventura@cnm.org.br>
     * @param object  $rsRecordSet
     * @param string  $stFiltro    Filtros alternativos que podem ser passados
     * @param string  $stOrder     Ordenacao do SQL
     * @param boolean $boTransacao Usar transacao
     *
     * @return object $rsRecordSet
     */
    public function listValoresTabela(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        $stSql = "
            SELECT cod_tipo
                 , exercicio
                 , cod_estrutural
                 , descricao
                 , tipo
                 , nivel
                 , rpps
                 , orcamento_1
                 , orcamento_2
                 , orcamento_3
                 , orcamento_4
                 , valor_1
                 , valor_2
                 , valor_3
                 , valor_4
              FROM " . $this->getDado('funcao') . "(" . $this->getDado('cod_ppa') . ",'" . $this->getDado('ano') . "')
                AS retorno( cod_tipo       INTEGER,
                            exercicio      VARCHAR(4),
                            cod_estrutural VARCHAR,
                            descricao      VARCHAR,
                            tipo           CHAR(1),
                            nivel          NUMERIC(1),
                            rpps           NUMERIC(1),
                            orcamento_1    NUMERIC(1),
                            orcamento_2    NUMERIC(1),
                            orcamento_3    NUMERIC(1),
                            orcamento_4    NUMERIC(1),
                            valor_1        NUMERIC,
                            valor_2        NUMERIC,
                            valor_3        NUMERIC,
                            valor_4        NUMERIC)
        ";
        return $this->executaRecuperaSql($stSql,$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

}

?>
