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
 * Classe de mapeamento da tabela PPA.PPA_RECEITA_RECURSO_VALOR
 * Data de Criação: 09/10/2008

 *
 * @author Marcio Medeiros <marcio.medeiros@cnm.org.br>

 * @package URBEM
 * @subpackage Mapeamento
 *
 * $Id: $
 *

 * Casos de uso: uc-02.09.05
 */
class TPPAReceitaRecursoValor extends Persistente
{

    /**
     * Método Construtor
     *
     * @ignore Atualizado para ticket #14131
     */
    public function __construct()
    {
        parent::Persistente();
        $this->setTabela('ppa.ppa_receita_recurso_valor');
        $this->setCampoCod('cod_recurso');
        $this->setComplementoChave('cod_receita, cod_ppa, exercicio, cod_conta, cod_entidade, cod_receita_dados, exercicio_recurso, ano');
        // campo, tipo, not_null, data_length, pk, fk
        $this->addCampo('cod_receita',       'integer', true, '',     true,  true);
        $this->addCampo('cod_ppa',           'integer', true, '',     true,  true);
        $this->addCampo('exercicio',         'char',    true, '4',    true,  true);
        $this->addCampo('cod_conta',         'integer', true, '',     true,  true);
        $this->addCampo('cod_entidade',      'integer', true, '',     true,  true);
        $this->addCampo('cod_receita_dados', 'integer', true, '',     true,  true);
        $this->addCampo('exercicio_recurso', 'char',    true, '4',    true,  true);
        $this->addCampo('cod_recurso',       'integer', true, '',     true,  true);
        $this->addCampo('ano',               'char',    true, '1',    true,  true);
        $this->addCampo('valor',             'numeric', true, '14,2', false, false);
    }

    /**
     * Retorna um registro específico da tabela ppa.ppa_receita_recurso_valor
     *
     * @return RecordSet
     */
    public function recuperaReceitaRecursoValor(&$rsRecordSet, $stFiltro="", $stOrder="", $boTransacao="")
    {
        return $this->executaRecupera("montaRecuperaReceitaRecursoValor", $rsRecordSet, $stFiltro, $stOrder, $boTransacao);
    }

    /**
     * Monta SQL para buscar os dados na tabela
     *
     * @return string
     */
    public function montaRecuperaReceitaRecursoValor()
    {
        $stSql = "  SELECT PRRV.ano, ";
        $stSql.= "         PRRV.valor ";
        $stSql.= "    FROM ppa.ppa_receita_recurso_valor PRRV";

        return $stSql;
    }

    /**
    * Retorna o valor total dos Recursos de uma Receita
    *
    * @return RecordSet
    */
    public function recuperaValorTotalReceitaRecurso(&$rsRecordSet, $stFiltro="", $stOrder="", $boTransacao="")
    {
        return $this->executaRecupera("montaRecuperaValorTotalReceitaRecurso", $rsRecordSet, $stFiltro, $stOrder, $boTransacao);
    }

    /**
    * Monta SQL para buscar os dados na tabela
    *
    * @return string
    */
    public function montaRecuperaValorTotalReceitaRecurso()
    {
        $stSql  = "  SELECT SUM( PRRV.valor) as total_valor ";
        $stSql .= "    FROM ppa.ppa_receita_recurso_valor PRRV";

        return $stSql;
    }

} // end of class
?>
