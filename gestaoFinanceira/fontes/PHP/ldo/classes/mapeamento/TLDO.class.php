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
 * Data de Criação: 06/03/2009
 * Copyright CNM - Confederação Nacional de Municípios
 *
 * @author Fellipe Esteves dos Santos <fellipe.santos>
 * @package GF
 * @subpackage LDO
 * @uc 02.10.00 - Manter LDO
 *
 * $Id: $
 *
 */

class TLDO extends Persistente
{
    /**
     * Método construtor
     * @access private
     */
    public function __construct()
    {
        parent::Persistente();

        $this->setTabela('ldo.ldo');
        $this->setCampoCod('cod_ppa');
        $this->setComplementoChave('ano');

        $this->addCampo('cod_ppa'  , 'integer'  , true, '' , true , true);
        $this->addCampo('ano'      , 'character', true, '1', true , false);
        $this->addCampo('timestamp', 'timestamp', true, '1', false, false);
    }

    /**
     * Método que constroi a string SQL para o metodo getTimestamp
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
    public function getTimestamp(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        $stSql = "
            SELECT NOW() AS timestamp
        ";

        return $this->executaRecuperaSql($stSql,$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function recuperaLDOHomologado(&$rsRecordSet, $stCriterio, $stOrdem = '', $boTransacao = '')
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql       = $this->montaRecuperaLDOHomologado($stCriterio).$stOrdem;
        $obErro      = $obConexao->executaSQL($rsRecordSet, $stSql, $boTransacao);

        return $obErro;
    }

    public function montaRecuperaLDOHomologado($stCriterio)
    {
        $stSQL  = "    SELECT  ldo.ano                            \n";
        $stSQL .= "      FROM ldo.ldo                            \n";
        $stSQL .= "INNER JOIN ldo.homologacao_ldo  as ldo_homo   \n";
        $stSQL .= "        ON ldo.ano = ldo_homo.ano             \n";
        $stSQL .= $stCriterio;

        return $stSQL;
    }

    public function recuperaExerciciosLDO(&$rsRecordSet, $stOrdem = '', $boTransacao = '')
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql       = $this->montaRecuperaExerciciosLDO().$stOrdem;
        $this->setDebug($stSql);
        $obErro      = $obConexao->executaSQL($rsRecordSet, $stSql, $boTransacao);

    return $obErro;
    }

    public function montaRecuperaExerciciosLDO()
    {
        $stSQL  = "\n  SELECT ano_ldo ";
        $stSQL .= "\n       , cod_ppa ";
        $stSQL .= "\n       , ano ";
        $stSQL .= "\n    FROM ( SELECT (CAST(ppa.ano_inicio AS INTEGER) + CAST(ldo.ano AS INTEGER)) - 1 AS ano_ldo ";
        $stSQL .= "\n                , ppa.cod_ppa ";
        $stSQL .= "\n                , ldo.ano ";
        $stSQL .= "\n             FROM ldo.ldo ";
        $stSQL .= "\n       INNER JOIN ppa.ppa ";
        $stSQL .= "\n               ON ppa.cod_ppa = ldo.cod_ppa ";
        $stSQL .= "\n       ) AS ldo ";
        $stSQL .= "\n   WHERE";
        if ($this->getDado('cod_ppa')) {
            $stSQL .= " ldo.cod_ppa = ".$this->getDado('cod_ppa')." AND ";
        }

        if ($this->getDado('exercicio')) {
            $stSQL .= " ldo.ano_ldo = ".$this->getDado('exercicio')." AND ";
        }

        if ($this->getDado('homologado')) {
            $stSQL .= " ldo.fn_verifica_homologacao_ldo(cod_ppa, ano) AND ";
        }

        $stSQL = substr($stSQL, 0, (strlen($stSQL)-5));

        return $stSQL;
    }

    public function recuperaDadosLDOPorExercicio(&$rsRecordSet, $stFiltro = '', $stOrder = '', $boTransacao = '')
    {
        return $this->executaRecupera("montaRecuperaDadosLDOPorExercicio", $rsRecordSet, $stFiltro, $stOrder, $boTransacao);
    }

    public function montaRecuperaDadosLDOPorExercicio()
    {
        $stExercicio = $this->getDado('exercicio');
        $stSQL   = "\n     SELECT '".$stExercicio."'::integer - ppa.ano_final::integer + 4 AS ano";
        $stSQL  .= "\n     	    , ppa.cod_ppa";
        $stSQL  .= "\n       FROM ppa.ppa";
        $stSQL  .= "\n INNER JOIN ldo.ldo";
        $stSQL  .= "\n         ON ldo.cod_ppa = ppa.cod_ppa";
        $stSQL  .= "\n      WHERE '".$stExercicio."' BETWEEN ppa.ano_inicio AND ppa.ano_final";
        $stSQL  .= "\n   GROUP BY '".$stExercicio."'::integer - ppa.ano_final::integer + 4";
        $stSQL  .= "\n     	    , ppa.cod_ppa";

        return $stSQL;
    }

}
