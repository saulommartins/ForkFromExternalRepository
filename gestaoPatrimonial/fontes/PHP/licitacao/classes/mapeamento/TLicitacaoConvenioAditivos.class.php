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
    * Data de Criação: 15/10/2007

    * @author Analista: Gelson W. Gonçalves
    * @author Desenvolvedor: Henrique Girardi dos Santos

    * @package URBEM
    * @subpackage

    $Id: TLicitacaoConvenioAditivos.class.php 60037 2014-09-25 20:01:28Z carlos.silva $

    * Casos de uso : uc-03.05.29
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TLicitacaoConvenioAditivos extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function TLicitacaoConvenioAditivos()
    {
        parent::Persistente();
        $this->setTabela("licitacao.convenio_aditivos");

        $this->setCampoCod('num_aditivo');
        $this->setComplementoChave('exercicio_convenio, num_convenio, exercicio');

        $this->AddCampo('exercicio_convenio'    , 'char'   , true, '4'   , true, true);
        $this->AddCampo('num_convenio'          , 'integer', true, ''    , true, true);
        $this->AddCampo('exercicio'             , 'char'   , true, '4'   , true, false);
        $this->AddCampo('num_aditivo'           , 'integer', true, ''    , true, false);
        $this->AddCampo('responsavel_juridico'  , 'integer', true, ''    , false, true);
        $this->AddCampo('dt_vigencia'           , 'date'   , true, ''    , false, false);
        $this->AddCampo('dt_assinatura'         , 'date'   , true, ''    , false, false);
        $this->AddCampo('inicio_execucao'       , 'date'   , true, ''    , false, false);
        $this->AddCampo('valor_convenio'        , 'numeric', true, '14,2', false, false);
        $this->AddCampo('objeto'                , 'char'   , true, '50'  , false, false);
        $this->AddCampo('observacao'            , 'char'   , true, '200' , false, false);
        $this->AddCampo('fundamentacao'         , 'char'   , true, '100' , false, false);
        $this->AddCampo('cod_norma_autorizativa','integer' ,false ,''    , false, false);
    }

    /**
    * recuperaConvenioListagem
    *
    * Executa o método executaRecupera, onde o 1º parâmetro passado executa o método do sql.
    *
    */
    public function recuperaConvenioListagem(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql = $this->montaConvenioListagemAditivos().$stFiltro.$stOrdem;
        $this->stDebug = $stSql;
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    /**
    * montaConvenioListagemAditivos
    *
    * método que monta o sql para retorno dos dados da tabela convenio_aditivos para a listagem dos dados.
    * @return string
    */
    public function montaConvenioListagemAditivos()
    {
        $stSql = " SELECT  convenio.num_convenio                                                          \n";
        $stSql.= "      , convenio.exercicio                                                              \n";
        $stSql.= "      , to_char(convenio.dt_assinatura, 'dd/mm/yyyy') as dt_assinatura                  \n";
        $stSql.= "      , convenio_aditivos.num_aditivo                                                   \n";
        $stSql.= "      , convenio_aditivos.exercicio as exercicio_aditivo                                \n";
        $stSql.= "      , to_char(convenio_aditivos.dt_assinatura, 'dd/mm/yyyy') as dt_assinatura_aditivo \n";
        $stSql.= "      , convenio_aditivos.responsavel_juridico                                          \n";
        $stSql.= "      , substr(trim(objeto.descricao),1,50) as objeto_descricao                         \n";
        $stSql.= "  FROM licitacao.convenio_aditivos                                                      \n";
        $stSql.= "  INNER JOIN licitacao.convenio                                                         \n";
        $stSql.= "          ON convenio_aditivos.exercicio_convenio = convenio.exercicio                  \n";
        $stSql.= "          AND convenio_aditivos.num_convenio = convenio.num_convenio                    \n";
        $stSql.= "  INNER JOIN compras.objeto                                                             \n";
        $stSql.= "          ON objeto.cod_objeto = convenio.cod_objeto                                    \n";
        
        if ( $this->getDado('num_participante') ) {
            $stSql .="        JOIN licitacao.participante_convenio                                            \n";
            $stSql .="          ON convenio.num_convenio = participante_convenio.num_convenio                 \n";
            $stSql .="         AND convenio.exercicio    = participante_convenio.exercicio                    \n";
        }
        
        $stSql.= "       WHERE 1=1                                                                        \n";

        if ( $this->getDado('num_convenio') ) {
            $stSql .= " AND convenio.num_convenio = ".$this->getDado('num_convenio')." \n";
        }

        if ( $this->getDado('exercicio') ) {
            $stSql .= " AND convenio.exercicio = '".$this->getDado('exercicio')."' \n";
        }

        if ( $this->getDado('dt_assinatura') ) {
            $stSql .= " AND convenio.dt_assinatura = to_date('".$this->getDado('dt_assinatura')."', 'dd/mm/yyyy') \n";
        }

        if ( $this->getDado('cgm_fornecedor') ) {
           $stSql .= " AND participante_convenio.cgm_fornecedor = ".$this->getDado('cgm_fornecedor')." \n";
        }

        if ( $this->getDado('num_aditivo') ) {
            $stSql .= " AND convenio_aditivos.num_aditivo = ".$this->getDado('num_aditivo')." \n";
        }

        if ( $this->getDado('exercicio') ) {
            $stSql .= " AND convenio_aditivos.exercicio = ".$this->getDado('exercicio')." \n";
        }

        if ( $this->getDado('num_participante') ) {
            $stSql .= " AND participante_convenio.cgm_fornecedor = ".$this->getDado('num_participante')." \n";
        }

        $stFiltro .=  " AND NOT EXISTS( SELECT 1                                                                  \n";
        $stFiltro .=  "              FROM licitacao.convenio_anulado                                         \n";
        $stFiltro .=  "             WHERE convenio.num_convenio = convenio_anulado.num_convenio              \n";
        $stFiltro .=  "               AND convenio.exercicio    = convenio_anulado.exercicio )               \n";

        $stFiltro .=  " AND NOT EXISTS( SELECT 1                                                             \n";
        $stFiltro .=  "                   FROM licitacao.rescisao_convenio                                   \n";
        $stFiltro .=  "                  WHERE convenio.num_convenio = rescisao_convenio.num_convenio        \n";
        $stFiltro .=  "                    AND convenio.exercicio    = rescisao_convenio.exercicio_convenio) \n";

        return $stSql;
    }

    /**
    * recuperaConvenioAditivo
    *
    * Executa o método executaRecupera, onde o 1º parâmetro passado executa o método do sql.
    *
    */
    public function recuperaConvenioAditivo(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        return $this->executaRecupera("montaConvenioAditivos",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    /**
    * montaConvenioAditivos
    *
    * método que monta o sql para retorno dos dados da tabela convenio_aditivos para mostrá-los
    * @return string
    */
    public function montaConvenioAditivos()
    {
        $stSql = "SELECT  convenio_aditivos.num_aditivo
                        , convenio_aditivos.exercicio as exercicio_aditivo
                        , convenio_aditivos.responsavel_juridico
                        , sw_cgm.nom_cgm as cgm_responsavel_juridico
                        , to_char(convenio_aditivos.dt_assinatura, 'dd/mm/yyyy') as dt_assinatura
                        , to_char(convenio_aditivos.inicio_execucao, 'dd/mm/yyyy') as inicio_execucao
                        , to_char(convenio_aditivos.dt_vigencia, 'dd/mm/yyyy') as dt_vigencia
                        , convenio_aditivos.objeto
                        , convenio_aditivos.observacao
                        , convenio_aditivos.fundamentacao
                        , convenio_aditivos.valor_convenio
                        , convenio_aditivos.cod_norma_autorizativa
                    FROM licitacao.convenio_aditivos
                    INNER JOIN sw_cgm
                            ON sw_cgm.numcgm = convenio_aditivos.responsavel_juridico
                    WHERE ";

        if ($this->getDado("num_aditivo")) {
            $stSql .= " convenio_aditivos.num_aditivo = ".$this->getDado("num_aditivo")." \nAND  ";
        }
        if ($this->getDado("exercicio_aditivo")) {
            $stSql .= " convenio_aditivos.exercicio = ".$this->getDado("exercicio_aditivo")." \nAND  ";
        }
        if ($this->getDado("num_convenio")) {
            $stSql .= " convenio_aditivos.num_convenio = ".$this->getDado("num_convenio")." \nAND  ";
        }
        if ($this->getDado("exercicio")) {
            $stSql .= " convenio_aditivos.exercicio_convenio = ".$this->getDado("exercicio")." \nAND  ";
        }

        $stSql = substr($stSql, 0, strlen($stFiltro)-6);

        return $stSql;
    }

    /**
    * recuperaMaximaAditivo
    *
    * Executa o método executaRecupera, onde o 1º parâmetro passado executa o método do sql.
    *
    */
    public function recuperaMaximaAditivo(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        return $this->executaRecupera("montaMaximaDataAditivo",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    /**
    * montaMaximaDataAditivo
    *
    * Método que busca a maior data do convenio_aditivos
    * @return string
    */
    public function montaMaximaDataAditivo()
    {
        $stSql = "SELECT to_char(MAX(dt_assinatura) , 'dd/mm/yyyy') as dt_assinatura "
             ."\n FROM licitacao.convenio_aditivos"
             ."\n WHERE ";

        if ($this->getDado("num_aditivo")) {
            $stSql .= " convenio_aditivos.num_aditivo = ".$this->getDado("num_aditivo")." \nAND  ";
        }

        if ($this->getDado("exercicio_convenio")) {
            $stSql .= " convenio_aditivos.exercicio_convenio = '".$this->getDado("exercicio_convenio")."' \nAND  ";
        }

        $stSql = substr($stSql, 0, strlen($stFiltro)-6);

        return $stSql;
    }

}
