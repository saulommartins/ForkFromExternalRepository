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
 * Mapeamento da tabela ldo.tipo_evolucao_patrimonio_liquido
 *
 * @category    Urbem
 * @package     LDO
 * @author      Analista        Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
 * @author      Desenvolvedor   Henrique Boaventura <henrique.boaventura@cnm.org.br>
 * $Id:$
 */

class TLDOTipoEvolucaoPatrimonioLiquido extends Persistente
{
    /**
     * Método construtor
     * @access private
     */
    public function __construct()
    {
        parent::Persistente();

        $this->setTabela('ldo.tipo_evolucao_patrimonio_liquido');

        $this->setCampoCod('');
        $this->setComplementoChave('cod_tipo,rpps');

        $this->addCampo('cod_tipo'              ,'integer'  ,true, ''  ,true ,false);
        $this->addCampo('rpps'                  ,'boolean'  ,true, ''  ,true,false);
        $this->addCampo('nivel'                 ,'numeric'  ,true, '1' ,false,false);
        $this->addCampo('descricao'             ,'varchar'  ,true, '30',false,false);

    }

    /**
     * Método que retorna os valores para entidades nao rpps
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
    public function listValores(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        $stSql = "
            SELECT cod_tipo
                 , descricao
                 , rpps
                 , nivel
                 , valor_1
                 , valor_2
                 , valor_3
                 , porcentagem_1
                 , porcentagem_2
                 , porcentagem_3
                 , orcamento_1
                 , orcamento_2
                 , orcamento_3
              FROM ldo.fn_evolucao_patrimonio_liquido(" . $this->getDado('cod_ppa') . ",'" . $this->getDado('ano') . "'," . $this->getDado('rpps') . ")
                AS retorno( cod_tipo        INTEGER
                           ,descricao       VARCHAR
                           ,rpps            NUMERIC
                           ,nivel           NUMERIC
                           ,valor_1         NUMERIC
                           ,valor_2         NUMERIC
                           ,valor_3         NUMERIC
                           ,porcentagem_1   NUMERIC
                           ,porcentagem_2   NUMERIC
                           ,porcentagem_3   NUMERIC
                           ,orcamento_1     NUMERIC
                           ,orcamento_2     NUMERIC
                           ,orcamento_3     NUMERIC);
        ";

        return $this->executaRecuperaSql($stSql,$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

}

?>
