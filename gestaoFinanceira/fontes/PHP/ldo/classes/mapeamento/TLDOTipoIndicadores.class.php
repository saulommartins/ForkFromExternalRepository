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
 * Classe Mapeameto do 02.10.00 - Manter LDO
 * Data de Criação: 09/03/2009
 * Copyright CNM - Confederação Nacional de Municípios
 *
 * @author Pedro Vaz de Mello de Medeiros <pedro.medeiros>
 * @package GF
 * @subpackage LDO
 * @uc 02.10.00 - Manter LDO
 */

class TLDOTipoIndicadores extends Persistente
{
    /**
     * Método construtor
     * @access private
     */
    public function __construct()
    {
        parent::Persistente();

        $this->setTabela('ldo.tipo_indicadores');
        $this->setCampoCod('cod_tipo_indicador');

        $this->addCampo('cod_tipo_indicador', 'integer', true, ''   , true , true);
        $this->addCampo('cod_unidade'       , 'integer', true, ''   , false, true);
        $this->addCampo('cod_grandeza'      , 'integer', true, ''   , false, true);
        $this->addCampo('descricao'         , 'varchar', true, '100', false, false);
    }

    public function recuperaSimboloTipoIndicador(&$rsRecordSet, $stFiltro = "", $stOrder = "", $boTransacao = "")
    {
        $stSql  = "\n     SELECT unidade_medida.simbolo";
        $stSql .= "\n       FROM ldo.tipo_indicadores";
        $stSql .= "\n INNER JOIN administracao.unidade_medida";
        $stSql .= "\n         ON unidade_medida.cod_unidade  = tipo_indicadores.cod_unidade";
        $stSql .= "\n        AND unidade_medida.cod_grandeza = tipo_indicadores.cod_grandeza";
        $stSql .= "\n      WHERE";

        if ($this->getDado('cod_tipo_indicador')) {
            $stSql .= ' tipo_indicadores.cod_tipo_indicador = '.$this->getDado('cod_tipo_indicador')." AND ";
        }

        if ($this->getDado('cod_unidade')) {
            $stSql .= ' tipo_indicadores.cod_unidade = '.$this->getDado('cod_unidade')." AND ";
        }

        if ($this->getDado('cod_grandeza')) {
            $stSql .= ' tipo_indicadores.cod_grandeza = '.$this->getDado('cod_grandeza')." AND ";
        }

        $stSql = substr($stSql, 0, (strlen($stSql)-5));
        $this->executaRecuperaSql($stSql, $rsRecordSet, $stFiltro, $stOrder, $boTransacao);
    }

    public function montaRecuperaRelacionamento()
    {
        $stSql  = "\n SELECT tipo_indicadores.cod_tipo_indicador";
        $stSql .= "\n      , tipo_indicadores.cod_unidade";
        $stSql .= "\n      , tipo_indicadores.cod_grandeza";
        $stSql .= "\n      , tipo_indicadores.descricao";
        $stSql .= "\n      , unidade_medida.nom_unidade";
        $stSql .= "\n      , unidade_medida.simbolo";
        $stSql .= "\n   FROM ldo.tipo_indicadores";
        $stSql .= "\n INNER JOIN administracao.unidade_medida";
        $stSql .= "\n         ON unidade_medida.cod_unidade  = tipo_indicadores.cod_unidade";
        $stSql .= "\n        AND unidade_medida.cod_grandeza = tipo_indicadores.cod_grandeza";
        $stSql .= "\n  WHERE";

        if ($this->getDado('cod_tipo_indicador')) {
            $stSql .= ' tipo_indicadores.cod_tipo_indicador = '.$this->getDado('cod_tipo_indicador').' AND ';
        }

        if ($this->getDado('cod_unidade')) {
            $stSql .= ' tipo_indicadores.cod_unidade = '.$this->getDado('cod_unidade').' AND ';
        }

        if ($this->getDado('cod_grandeza')) {
            $stSql .= ' tipo_indicadores.cod_grandeza = '.$this->getDado('cod_grandeza').' AND ';
        }

        if ($this->getDado('descricao')) {
            $stSql .= ' tipo_indicadores.descricao ilike \''.$this->getDado('descricao').'\' AND ';
        }

        return substr($stSql, 0, (strlen($stSql)-5));
    }

    public function recuperaIndicadores(&$rsRecordSet, $stFiltro = '', $stOrder = '', $boTransacao = '')
    {
        return $this->executaRecupera("montaRecuperaIndicadores", $rsRecordSet, $stFiltro, $stOrder, $boTransacao);
    }

    public function montaRecuperaIndicadores()
    {
        $stSql  = "\n SELECT tipo_indicadores.cod_tipo_indicador";
        $stSql .= "\n      , tipo_indicadores.cod_unidade";
        $stSql .= "\n      , tipo_indicadores.cod_grandeza";
        $stSql .= "\n      , tipo_indicadores.descricao";
        $stSql .= "\n      , unidade_medida.nom_unidade";
        $stSql .= "\n      , unidade_medida.simbolo";
        $stSql .= "\n   FROM ldo.tipo_indicadores";
        $stSql .= "\n INNER JOIN administracao.unidade_medida";
        $stSql .= "\n         ON unidade_medida.cod_unidade  = tipo_indicadores.cod_unidade";
        $stSql .= "\n        AND unidade_medida.cod_grandeza = tipo_indicadores.cod_grandeza";

        $stSql .= "\n INNER JOIN ldo.indicadores";
        $stSql .= "\n        ON indicadores.cod_tipo_indicador = tipo_indicadores.cod_tipo_indicador";
        $stSql .= "\n  WHERE";

        if ($this->getDado('cod_tipo_indicador')) {
            $stSql .= ' tipo_indicadores.cod_tipo_indicador = '.$this->getDado('cod_tipo_indicador').' AND ';
        }

        if ($this->getDado('cod_unidade')) {
            $stSql .= ' tipo_indicadores.cod_unidade = '.$this->getDado('cod_unidade').' AND ';
        }

        if ($this->getDado('cod_grandeza')) {
            $stSql .= ' tipo_indicadores.cod_grandeza = '.$this->getDado('cod_grandeza').' AND ';
        }

        if ($this->getDado('descricao')) {
            $stSql .= " tipo_indicadores.descricao ilike '%".$this->getDado('descricao')."%' AND ";
        }

        if ($this->getDado('exercicio')) {
            $stSql .= ' indicadores.exercicio ilike \''.$this->getDado('exercicio').'\' AND ';
        }

        return $stSQL;
    }
}
