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
  * Classe de mapeamento da tabela FOLHAPAGAMENTO.SALARIO_FAMILIA
  * Data de Criação: 19/04/2006

  * @author Analista: Vandre Miguel Ramos
  * @author Desenvolvedor: Andre Almeida

  * @package URBEM
  * @subpackage Mapeamento

  Caso de uso: uc-04.05.44
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela FOLHAPAGAMENTO.SALARIO_FAMILIA
  * Data de Criação: 19/04/2006

  * @author Analista: Vandre Miguel Ramos
  * @author Desenvolvedor: Andre Almeida

  * @package URBEM
  * @subpackage Mapeamento
*/
class TFolhaPagamentoSalarioFamilia extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
    public function TFolhaPagamentoSalarioFamilia()
    {
        parent::Persistente();
        $this->setTabela('folhapagamento.salario_familia');

        $this->setCampoCod('cod_regime_previdencia');
        $this->setComplementoChave('');

        $this->AddCampo('cod_regime_previdencia', 'integer'  , true, '',  true,  true );
        $this->AddCampo('vigencia'              , 'date'     , true, '', false, false );
        $this->AddCampo('idade_limite'          , 'integer'  , true, '', false, false );
    }

    public function recuperaSalarioFamiliaMaxTimestamp(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql = $this->montaRecuperaSalarioFamiliaMaxTimestamp().$stFiltro.$stOrdem;
        $this->stDebug = $stSql;
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function recuperaSalarioFamiliaVigencia(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql = $this->montaRecuperaSalarioFamiliaVigencia().$stFiltro.$stOrdem;
        $this->stDebug = $stSql;
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function recuperaRelacionamento(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql = $this->montaRecuperaRelacionamento().$stFiltro.$stOrdem;
        $this->stDebug = $stSql;
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaSalarioFamiliaMaxTimestamp()
    {
        $stSql  = " SELECT fsf.cod_regime_previdencia                               \n";
        $stSql .= "      , frp.descricao as descricao_regime_previdencia            \n";
        $stSql .= "      , to_char(fsf.vigencia,'dd/mm/yyyy') as vigencia           \n";
        $stSql .= "      , fsf.idade_limite                                         \n";
        $stSql .= "      , fsf.timestamp                                            \n";
        $stSql .= "   FROM folhapagamento.salario_familia fsf                       \n";
        $stSql .= "   JOIN (   SELECT max_fsf.cod_regime_previdencia                \n";
        $stSql .= "                 , MAX(max_fsf.timestamp) as timestamp           \n";
        $stSql .= "              FROM folhapagamento.salario_familia max_fsf        \n";
        $stSql .= "          GROUP BY max_fsf.cod_regime_previdencia                \n";
        $stSql .= "        ) as mfsf                                                \n";
        $stSql .= "     ON mfsf.cod_regime_previdencia = fsf.cod_regime_previdencia \n";
        $stSql .= "    AND mfsf.timestamp              = fsf.timestamp              \n";
        $stSql .= "   JOIN folhapagamento.regime_previdencia frp                    \n";
        $stSql .= "     ON frp.cod_regime_previdencia  = fsf.cod_regime_previdencia \n";

        return $stSql;
    }

    public function montaRecuperaSalarioFamiliaVigencia()
    {
        $stSql  = " SELECT fsf.cod_regime_previdencia                                        \n";
        $stSql .= "      , frp.descricao as descricao_regime_previdencia                     \n";
        $stSql .= "      , to_char(fsf.vigencia,'dd/mm/yyyy') as vigencia                    \n";
        $stSql .= "      , fsf.idade_limite                                                  \n";
        $stSql .= "      , fsf.timestamp                                                     \n";
        $stSql .= "   FROM folhapagamento.salario_familia fsf                                \n";
        $stSql .= "   JOIN (   SELECT max_fsf.cod_regime_previdencia                         \n";
        $stSql .= "                 , max_fsf.vigencia                                       \n";
        $stSql .= "                 , MAX(max_fsf.timestamp) as timestamp                    \n";
        $stSql .= "              FROM folhapagamento.salario_familia max_fsf                 \n";
        $stSql .= "          GROUP BY max_fsf.cod_regime_previdencia, max_fsf.vigencia       \n";
        $stSql .= "        ) as mfsf                                                         \n";
        $stSql .= "     ON mfsf.cod_regime_previdencia = fsf.cod_regime_previdencia          \n";
        $stSql .= "    AND mfsf.timestamp              = fsf.timestamp                       \n";
        $stSql .= "    AND mfsf.vigencia               = fsf.vigencia                        \n";
        $stSql .= "   JOIN folhapagamento.regime_previdencia frp                             \n";
        $stSql .= "     ON frp.cod_regime_previdencia  = fsf.cod_regime_previdencia          \n";
        $stSql .= "  WHERE fsf.vigencia >= COALESCE (                                        \n";
        $stSql .= "                                   (SELECT vigencia                       \n";
        $stSql .= "                                     from folhapagamento.salario_familia                                                                        \n";
        $stSql .= "                                    WHERE vigencia = to_date('".$this->getDado("vigencia")."', 'dd/mm/yyyy')  order by vigencia limit 1 ),      \n";
        $stSql .= "                                   (SELECT vigencia                                                                                             \n";
        $stSql .= "                                      from folhapagamento.salario_familia                                                                       \n";
        $stSql .= "                                     WHERE vigencia < to_date('".$this->getDado("vigencia")."', 'dd/mm/yyyy') order by vigencia desc limit 1 )  \n";
        $stSql .= "                                 )                                                                                                              \n";
        if ( $this->getDado("cod_regime_previdencia") )
           $stSql .= " AND fsf.cod_regime_previdencia = ".$this->getDado("cod_regime_previdencia")."                                                               \n";

        return $stSql;
    }

    public function montaRecuperaRelacionamento()
    {
        $stSql  = " SELECT fsf.cod_regime_previdencia                               \n";
        $stSql .= "      , fsf.timestamp                                            \n";
        $stSql .= "      , frp.descricao as descricao_regime_previdencia            \n";
        $stSql .= "      , to_char(fsf.vigencia,'dd/mm/yyyy') as vigencia           \n";
        $stSql .= "      , fsf.idade_limite                                         \n";
        $stSql .= "   FROM folhapagamento.salario_familia fsf                       \n";
        $stSql .= "   JOIN folhapagamento.regime_previdencia frp                    \n";
        $stSql .= "     ON frp.cod_regime_previdencia  = fsf.cod_regime_previdencia \n";

        return $stSql;
    }

}
