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
    * Página de

    * Data de Criação   : 26/12/2007

    * @author Analista: Gelson Wolvowski Gonçalves
    * @author Desenvolvedor: Henrique Girardi dos Santos

    * Id:$

    * Casos de uso: uc-06.07.00
*/

require_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
require_once CLA_PERSISTENTE;
require_once CAM_GPC_TCMPA_MAPEAMENTO."TTPATipoUnidadeGestora.class.php";

/**
  *
  * Data de Criação: 26/12/2007

  * @author Analista: Gelson Wolvowski
  * @author Desenvolvedor: Henrique Girardi dos Santos

*/

class TTPAUnidadeGestora extends TTPATipoUnidadeGestora
{

    /**
        * Método Construtor
        * @access Private
    */
    public function TTPAUnidadeGestora()
    {
        parent::TTPATipoUnidadeGestora();
    }

    public function recuperaDadosUnidadeGestora(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stSql = $this->montarRecuperaDadosUnidadeGestora().$stCondicao.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montarRecuperaDadosUnidadeGestora()
    {
        $stSql = "\n  SELECT  '010' AS tipo_registro "
                ."\n    ,   orcamento.orgao.cod_orgao "
                ."\n    ,   orcamento.orgao.nom_orgao "
                ."\n    ,   orgao_unidade_gestora.cod_tipo "
                ."\n    ,   orgao_unidade_gestora.unidade_gestora "
                ."\n    ,   sw_cgm_pessoa_juridica.cnpj "
                ."\n    ,   '*' AS fim_registro "
                ."\n  FROM orcamento.orgao "

                ."\n  INNER JOIN  tcmpa.orgao_unidade_gestora "
                ."\n          ON  orgao_unidade_gestora.exercicio = orcamento.orgao.exercicio "
                ."\n          AND orgao_unidade_gestora.num_orgao = orcamento.orgao.num_orgao "

                ."\n  INNER JOIN  sw_cgm_pessoa_juridica "
                ."\n          ON  sw_cgm_pessoa_juridica.numcgm = tcmpa.orgao_unidade_gestora.numcgm "
                ."\n  WHERE orgao_unidade_gestora.num_orgao = ".$this->getDado('num_orgao')
                ."\n    AND orgao_unidade_gestora.exercicio = ".$this->getDado('exercicio');

        $stSql .= "\n  ORDER BY orcamento.orgao.cod_orgao";

        return $stSql;
    }

     function recuperaListagemEntidades(&$rsRecordSet, $stFiltro="", $stOrder="", $boTransacao="")
    {
         return $this->executaRecupera("montaRecuperaListagemEntidades", $rsRecordSet, $stFiltro, $stOrder, $boTransacao);
    }

    public function montaRecuperaListagemEntidades()
    {
        $stSql  = "\n SELECT entidade.cod_entidade"
                 ."\n     ,  sw_cgm.nom_cgm AS descricao"
                 ."\n     ,  unidade_gestora.valor AS unidade_gestora"
                 ."\n     ,  tipo_unidade_gestora.valor AS cod_tipo"

                 ."\n FROM orcamento.entidade "

                 ."\n LEFT JOIN administracao.configuracao_entidade AS unidade_gestora"
                 ."\n        ON entidade.cod_entidade      = unidade_gestora.cod_entidade"
                 ."\n       AND entidade.exercicio         = unidade_gestora.exercicio"
                 ."\n       AND unidade_gestora.parametro  = 'tcm_unidade_gestora'"
                 ."\n       AND unidade_gestora.cod_modulo = 48 -- TCM - PA"

                 ."\n LEFT JOIN administracao.configuracao_entidade AS tipo_unidade_gestora"
                 ."\n        ON entidade.cod_entidade           = tipo_unidade_gestora.cod_entidade"
                 ."\n       AND entidade.exercicio              = tipo_unidade_gestora.exercicio"
                 ."\n       AND tipo_unidade_gestora.parametro  = 'tcm_tipo_unidade_gestora' "
                 ."\n       AND tipo_unidade_gestora.cod_modulo = 48 -- TCM - PA"

                 ."\n , sw_cgm"
                 ."\n WHERE sw_cgm.numcgm = entidade.numcgm "
                 ."\n  AND entidade.exercicio = ".$this->getDado('exercicio')

                 //."\n  AND EXISTS ( SELECT entidade_rh.cod_entidade"
                 //."\n               FROM administracao.entidade_rh"
                 //."\n               WHERE entidade_rh.cod_entidade = entidade.cod_entidade"
                 //."\n                 AND entidade_rh.exercicio = entidade.exercicio)"
                 ."\n ORDER BY cod_entidade";

        return $stSql;
    }

}
