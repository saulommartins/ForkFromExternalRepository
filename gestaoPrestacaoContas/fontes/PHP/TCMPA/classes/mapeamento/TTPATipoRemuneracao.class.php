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
    * Classe de mapeamento da tabela TCMPA.TIPO_REMUNERACAO
    * Data de Criacao: 21/01/2008

    * @author Analista: Gelson W. Golcalves
    * @author Desenvolvedor: Henrique Girardi dos Santos

    * @package URBEM
    * @subpackage Mapeamento

    $Id:$

    * Casos de uso: uc-06.07.00
*/

require_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
require_once CLA_PERSISTENTE;

/**
  * Efetua conexao com a tabela  TCMPA.TIPO_UNIDADE_GESTORA
  * Data de Criacao: 21/01/2008

  * @author Analista: Gelson W. Golcalves
  * @author Desenvolvedor: Henrique Girardi dos Santos

*/
class TTPATipoRemuneracao extends Persistente
{

    /**
      * Método Construtor
      * @access Private
    */
    public function TTPATipoRemuneracao()
    {
        parent::Persistente();
        $this->setTabela('tcmpa'.Sessao::getEntidade().'.tipo_remuneracao');

        $this->setCampoCod('');
        $this->setComplementoChave('codigo');

        $this->AddCampo( 'codigo'   , 'integer', true, '4', true , false );
        $this->AddCampo( 'descricao', 'char'   , true, '' , false, false );
    }

    public function recuperaEventosCalculados(&$rsRecordSet, $stFiltro="", $stOrder="", $boTransacao="")
    {
        return $this->executaRecupera("montaRecuperaEventosCalculados", $rsRecordSet, $stFiltro, $stOrder, $boTransacao);
    }

    public function montaRecuperaEventosCalculados()
    {
        $stSql  = "SELECT evento_calculado.*                                                    \n";
        $stSql .= "  FROM folhapagamento".Sessao::getEntidade().".registro_evento_periodo      \n";
        $stSql .= "     , folhapagamento".Sessao::getEntidade().".evento_calculado             \n";
        $stSql .= "     , folhapagamento".Sessao::getEntidade().".evento                       \n";
        $stSql .= " WHERE registro_evento_periodo.cod_registro = evento_calculado.cod_registro  \n";
        $stSql .= "   AND evento_calculado.cod_evento = evento.cod_evento                       \n";

    return $stSql;
    }

    public function recuperaEventosFeriasCalculados(&$rsRecordSet, $stFiltro="", $stOrder="", $boTransacao="")
    {
        return $this->executaRecupera("montaRecuperaEventosFeriasCalculados", $rsRecordSet, $stFiltro, $stOrder, $boTransacao);
    }

    public function montaRecuperaEventosFeriasCalculados()
    {
        $stSql  = "SELECT evento_ferias_calculado.*                                                     \n";
        $stSql .= "  FROM folhapagamento".Sessao::getEntidade().".registro_evento_ferias               \n";
        $stSql .= "     , folhapagamento".Sessao::getEntidade().".evento_ferias_calculado              \n";
        $stSql .= "     , folhapagamento".Sessao::getEntidade().".evento                               \n";
        $stSql .= " WHERE registro_evento_ferias.cod_registro = evento_ferias_calculado.cod_registro    \n";
        $stSql .= "   AND registro_evento_ferias.cod_evento = evento_ferias_calculado.cod_evento        \n";
        $stSql .= "   AND registro_evento_ferias.desdobramento = evento_ferias_calculado.desdobramento  \n";
        $stSql .= "   AND registro_evento_ferias.timestamp = evento_ferias_calculado.timestamp_registro \n";
        $stSql .= "   AND evento_ferias_calculado.cod_evento = evento.cod_evento                        \n";

    return $stSql;
    }

    public function recuperaEventosDecimoCalculados(&$rsRecordSet, $stFiltro="", $stOrder="", $boTransacao="")
    {
        return $this->executaRecupera("montaRecuperaEventosDecimoCalculados", $rsRecordSet, $stFiltro, $stOrder, $boTransacao);
    }

    public function montaRecuperaEventosDecimoCalculados()
    {
        $stSql  = "SELECT evento_decimo_calculado.*                                                     \n";
        $stSql .= "  FROM folhapagamento".Sessao::getEntidade().".registro_evento_decimo               \n";
        $stSql .= "     , folhapagamento".Sessao::getEntidade().".evento_decimo_calculado              \n";
        $stSql .= "     , folhapagamento".Sessao::getEntidade().".evento                               \n";
        $stSql .= " WHERE registro_evento_decimo.cod_registro = evento_decimo_calculado.cod_registro    \n";
        $stSql .= "   AND registro_evento_decimo.cod_evento = evento_decimo_calculado.cod_evento        \n";
        $stSql .= "   AND registro_evento_decimo.desdobramento = evento_decimo_calculado.desdobramento  \n";
        $stSql .= "   AND registro_evento_decimo.timestamp = evento_decimo_calculado.timestamp_registro \n";
        $stSql .= "   AND evento_decimo_calculado.cod_evento = evento.cod_evento                        \n";

    return $stSql;
    }

    public function recuperaEventosComplementarCalculados(&$rsRecordSet, $stFiltro="", $stOrder="", $boTransacao="")
    {
        return $this->executaRecupera("montaRecuperaEventosComplementarCalculados", $rsRecordSet, $stFiltro, $stOrder, $boTransacao);
    }

    public function montaRecuperaEventosComplementarCalculados()
    {
        $stSql  = "SELECT evento_complementar_calculado.*                                                     \n";
        $stSql .= "  FROM folhapagamento".Sessao::getEntidade().".registro_evento_complementar               \n";
        $stSql .= "     , folhapagamento".Sessao::getEntidade().".evento_complementar_calculado              \n";
        $stSql .= "     , folhapagamento".Sessao::getEntidade().".evento                               \n";
        $stSql .= " WHERE registro_evento_complementar.cod_registro = evento_complementar_calculado.cod_registro    \n";
        $stSql .= "   AND registro_evento_complementar.cod_evento = evento_complementar_calculado.cod_evento        \n";
        $stSql .= "   AND registro_evento_complementar.cod_configuracao = evento_complementar_calculado.cod_configuracao  \n";
        $stSql .= "   AND registro_evento_complementar.timestamp = evento_complementar_calculado.timestamp_registro \n";
        $stSql .= "   AND evento_complementar_calculado.cod_evento = evento.cod_evento                        \n";

    return $stSql;
    }

    public function recuperaEventosRecisaoCalculados(&$rsRecordSet, $stFiltro="", $stOrder="", $boTransacao="")
    {
        return $this->executaRecupera("montaRecuperaEventosRecisaoCalculados", $rsRecordSet, $stFiltro, $stOrder, $boTransacao);
    }

    public function montaRecuperaEventosRecisaoCalculados()
    {
        $stSql  = "SELECT evento_rescisao_calculado.*                                                     \n";
        $stSql .= "  FROM folhapagamento".Sessao::getEntidade().".registro_evento_rescisao               \n";
        $stSql .= "     , folhapagamento".Sessao::getEntidade().".evento_rescisao_calculado              \n";
        $stSql .= "     , folhapagamento".Sessao::getEntidade().".evento                               \n";
        $stSql .= " WHERE registro_evento_rescisao.cod_registro = evento_rescisao_calculado.cod_registro    \n";
        $stSql .= "   AND registro_evento_rescisao.cod_evento = evento_rescisao_calculado.cod_evento        \n";
        $stSql .= "   AND registro_evento_rescisao.desdobramento = evento_rescisao_calculado.desdobramento  \n";
        $stSql .= "   AND registro_evento_rescisao.timestamp = evento_rescisao_calculado.timestamp_registro \n";
        $stSql .= "   AND evento_rescisao_calculado.cod_evento = evento.cod_evento                        \n";

    return $stSql;
    }

    public function recuperaEventos(&$rsRecordSet, $stFiltro="", $stOrder="", $boTransacao="")
    {
        return $this->executaRecupera("montaRecuperaEventos", $rsRecordSet, $stFiltro, $stOrder, $boTransacao);
    }

    public function montaRecuperaEventos()
    {

        $stSql = "select tipo_remuneracao_evento.*, evento.descricao";
        $stSql .="  from tcmpa".Sessao::getEntidade().".tipo_remuneracao_evento , folhapagamento".Sessao::getEntidade().".evento";
        $stSql .=" where tipo_remuneracao_evento.cod_evento = evento.cod_evento";

        return $stSql;
    }

    public function recuperaDataPagamento(&$rsRecordSet, $stFiltro="", $stOrder="", $boTransacao="")
    {
        return $this->executaRecupera("montaRecuperaDataPagamento",$rsRecordSet, $stFiltro, $stOrdem, $boTransacao);
    }

    public function montaRecuperaDataPagamento()
    {
        $stSql  = "SELECT to_char(dt_final,'ddmmyyyy') as dt_final                          \n";
        $stSql .= "     , cod_periodo_movimentacao                                          \n";
        $stSql .= "  FROM folhapagamento".Sessao::getEntidade().".periodo_movimentacao     \n";

        return $stSql;
    }

    public function recuperaCodEventoPrevidencia(&$rsRecordSet, $stFiltro="", $stOrder="", $boTransacao="")
    {
        return $this->executaRecupera("montaRecuperaCodEventoPrevidencia",$rsRecordSet, $stFiltro, $stOrdem, $boTransacao);
    }

    public function montaRecuperaCodEventoPrevidencia()
    {
        $stSql = "SELECT previdencia_evento.cod_evento";
        $stSql .="FROM folhapagamento.previdencia_evento";
        $stSql .="	, (SELECT cod_previdencia";
        $stSql .="  	    , MAX(timestamp) as timestamp";
        $stSql .="	   FROM folhapagamento.previdencia_evento";
        $stSql .="	   GROUP BY cod_previdencia";
        $stSql .="	   ) AS max_previdencia_envento";
        $stSql .="WHERE cod_tipo = 1";
        $stSql .="  AND previdencia_evento.cod_previdencia = max_previdencia_envento.cod_previdencia";
        $stSql .="  AND previdencia_evento.timestamp = max_previdencia_envento.timestamp";
        $stSql .="  AND previdencia_evento.cod_previdencia = (SELECT previdencia_previdencia.cod_previdencia";
        $stSql .="                                                FROM pessoal.contrato_servidor_previdencia";
        $stSql .="                                              , (SELECT cod_contrato";
        $stSql .="                                                           , MAX(timestamp) as timestamp";
        $stSql .="                                                        FROM pessoal.contrato_servidor_previdencia";
        $stSql .="                                                       GROUP BY cod_contrato";
        $stSql .="                                                      ) AS max_contrato_servidor_previdencia";
        $stSql .="			                                    , folhapagamento.previdencia_previdencia";
        $stSql .="			                                    , (SELECT cod_previdencia";
        $stSql .="				                                            , MAX(timestamp) as timestamp";
        $stSql .="				                                   FROM folhapagamento.previdencia_previdencia";
           $stSql .="			                                       GROUP BY cod_previdencia";
        $stSql .="				                               	   ) AS max_previdencia_previdencia";
        $stSql .="                                              WHERE contrato_servidor_previdencia.cod_contrato =".$this->getDado('cod_contrato');
        $stSql .="                                                AND contrato_servidor_previdencia.cod_contrato = max_contrato_servidor_previdencia.cod_contrato";
        $stSql .="                                                AND contrato_servidor_previdencia.timestamp = max_contrato_servidor_previdencia.timestamp";
        $stSql .="                                                AND previdencia_previdencia.cod_previdencia = max_previdencia_previdencia.cod_previdencia";
        $stSql .="                                                AND previdencia_previdencia.timestamp = max_previdencia_previdencia.timestamp";
        $stSql .="                                                AND contrato_servidor_previdencia.cod_previdencia = previdencia_previdencia.cod_previdencia";
        $stSql .="                                                AND previdencia_previdencia.tipo_previdencia = 'o')";

        return $stSql;
    }

    public function recuperaContribuicaoPrevidencia(&$rsRecordSet, $stFiltro="", $stOrder="", $boTransacao="")
    {
        return $this->executaRecupera("montaRecuperaContribuicaoPrevidencia", $rsRecordSet, $stFiltro, $stOrdem, $boTransacao);
    }

    public function montaRecuperaContribuicaoPrevidencia()
    {
        $stSql =" SELECT coalesce(sum(evento_calculado.valor),0) as valores";
        $stSql .="   FROM folhapagamento".Sessao::getEntidade().".evento_calculado";
        $stSql .="      , folhapagamento".Sessao::getEntidade().".registro_evento_periodo, folhapagamento".Sessao::getEntidade().".periodo_movimentacao";
        $stSql .="  where periodo_movimentacao.cod_periodo_movimentacao = registro_evento_periodo.cod_periodo_movimentacao";
        $stSql .="    and registro_evento_periodo.cod_registro = evento_calculado.cod_registro";

        return $stSql;
    }

    public function recuperaTotalDesconto(&$rsRecordSet, $stFiltro="", $stOrder="", $boTransacao="")
    {
        return $this->executaRecupera("montaRecuperaTotalDescontos"	, $rsRecordSet, $stFiltro, $stOrdem, $boTransacao);
    }

    public function montaRecuperaTotalDescontos()
    {
        $stSql ="SELECT coalesce(sum(evento_calculado.valor),0) as valores";
        $stSql.="  FROM folhapagamento".Sessao::getEntidade().".evento";
        $stSql.="     , folhapagamento".Sessao::getEntidade().".registro_evento";
        $stSql.="     , folhapagamento".Sessao::getEntidade().".ultimo_registro_evento ";
        $stSql.="     , folhapagamento".Sessao::getEntidade().".evento_calculado ";
        $stSql.="     , folhapagamento".Sessao::getEntidade().".registro_evento_periodo";
        $stSql.="     , folhapagamento".Sessao::getEntidade().".periodo_movimentacao";

        $stSql.=" where evento.cod_evento          =  registro_evento.cod_evento";
        $stSql.="   and registro_evento.cod_evento   = ultimo_registro_evento.cod_evento";
        $stSql.="   and registro_evento.cod_registro = ultimo_registro_evento.cod_registro";
        $stSql.="   and registro_evento.timestamp    = ultimo_registro_evento.timestamp";

        $stSql.="   and registro_evento.cod_evento   = evento_calculado.cod_evento";
        $stSql.="   and registro_evento.cod_registro = evento_calculado.cod_registro";
        $stSql.="   and registro_evento.timestamp    = evento_calculado.timestamp_registro";

        $stSql.="   and registro_evento.cod_registro = registro_evento_periodo.cod_registro";
        $stSql.="   and periodo_movimentacao.cod_periodo_movimentacao = registro_evento_periodo.cod_periodo_movimentacao";

        $stSql.="   and evento.natureza = 'D' \n";

        return $stSql;
    }

    public function recuperaValoresDecimo(&$rsRecordSet, $stFiltro="", $stOrder="", $boTransacao="")
    {
        return $this->executaRecupera("montaRecuperaValoresDecimo",$rsRecordSet, $stFiltro, $stOrdem, $boTransacao);
    }

    public function montaRecuperaValoresDecimo()
    {
        $stSql = "select sum(evento_decimo_calculado.valor) as valores";
        $stSql .="  from folhapagamento".Sessao::getEntidade().".registro_evento_decimo";
        $stSql .="     , folhapagamento".Sessao::getEntidade().".evento_decimo_calculado";
        $stSql .="     , folhapagamento".Sessao::getEntidade().".periodo_movimentacao";
        $stSql .=" where periodo_movimentacao.cod_periodo_movimentacao = registro_evento_decimo.cod_periodo_movimentacao";
        $stSql .="   and registro_evento_decimo.cod_registro = evento_decimo_calculado.cod_registro";

        return $stSql;
    }

    public function recuperaCodEventoIrrf(&$rsRecordSet, $stFiltro="", $stOrder="", $boTransacao="")
    {
        return $this->executaRecupera("montaRecuperaCodEventoIrrf" , $rsRecordSet, $stFiltro, $stOrdem, $boTransacao);
    }

    public function montaRecuperaCodEventoIrrf()
    {
        $stSql = "SELECT tabela_irrf_evento.cod_evento";
        $stSql .="  FROM folhapagamento".Sessao::getEntidade().".tabela_irrf_evento";
        $stSql .="     , (SELECT cod_evento";
        $stSql .="	      , MAX(timestamp) as timestamp";
        $stSql .="	   FROM folhapagamento".Sessao::getEntidade().".tabela_irrf_evento";
        $stSql .="	  GROUP BY cod_evento";
        $stSql .="	 ) AS max_irrf_envento";
        $stSql .=" WHERE tabela_irrf_evento.cod_evento = max_irrf_envento.cod_evento";
        $stSql .="   AND tabela_irrf_evento.timestamp  = max_irrf_envento.timestamp";

        return $stSql;
    }

    public function recuperaNumeroDependentesIr(&$rsRecordSet, $stFiltro="", $stOrder="", $boTransacao="")
    {
        return $this->executaRecupera("montaRecuperaNumeroDependentesIr",$rsRecordSet, $stFiltro, $stOrdem, $boTransacao);
    }

    public function montaRecuperaNumeroDependentesIr()
    {
        $stSql =" SELECT coalesce( COUNT(sd.cod_servidor),0 ) as qtd_dependentes";
        $stSql.="   FROM pessoal.servidor_contrato_servidor";
        $stSql.="      , pessoal.servidor_dependente  as sd";
        $stSql.="   LEFT OUTER JOIN pessoal.dependente as d";
        $stSql.="                ON d.cod_dependente = sd.cod_dependente";
        $stSql.="   LEFT OUTER JOIN public.sw_cgm_pessoa_fisica as pf";
        $stSql.="                ON d.numcgm = pf.numcgm";
        $stSql.="   LEFT OUTER JOIN folhapagamento.vinculo_irrf as vi";
        $stSql.="                ON vi.cod_vinculo = d.cod_vinculo";
        $stSql.="   LEFT OUTER JOIN pessoal.dependente_excluido as de";
        $stSql.="                ON sd.cod_dependente = de.cod_dependente";
        $stSql.="               AND sd.cod_servidor = de.cod_servidor";
        $stSql.="  WHERE servidor_contrato_servidor.cod_contrato =".$this->getDado('cod_contrato');
        $stSql.="    AND servidor_contrato_servidor.cod_servidor = sd.cod_servidor";
        $stSql.="    AND pf.dt_nascimento is not null";
        $stSql.="    AND d.cod_vinculo > 0";
        $stSql.="    AND ( vi.idade_limite = 0";
        $stSql.="     OR (idade( to_char(pf.dt_nascimento,'yyyy-mm' ), substr('".$this->getDado('dt_final')."',1,7))) <= vi.idade_limite )";
        $stSql.="    AND de.cod_servidor is null";

        return $stSql;
    }
}
