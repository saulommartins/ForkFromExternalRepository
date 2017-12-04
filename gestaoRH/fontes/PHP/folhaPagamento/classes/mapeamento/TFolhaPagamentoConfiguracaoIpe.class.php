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
    * Classe de mapeamento da tabela folhapagamento.configuracao_ipe
    * Data de Criação: 23/06/2008

    * @author Desenvolvedor: Diego Lemos de Souza

    * Casos de uso: uc-04.05.66

    $Id: TFolhaPagamentoConfiguracaoIpe.class.php 59612 2014-09-02 12:00:51Z gelson $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GA_ADM_MAPEAMENTO."TAdministracaoCadastro.class.php" );
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  folhapagamento.configuracao_ipe
  * Data de Criação: 23/06/2008

  * @author Desenvolvedor: Diego Lemos de Souza

  * @package URBEM
  * @subpackage Mapeamento
*/
class TFolhaPagamentoConfiguracaoIpe extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function TFolhaPagamentoConfiguracaoIpe()
    {
        parent::Persistente();
        $this->setTabela("folhapagamento.configuracao_ipe");

        $this->setCampoCod('');
        $this->setComplementoChave('cod_configuracao, vigencia');

        $this->AddCampo('cod_configuracao'     ,'sequence',true  ,''     ,true,false);
        $this->AddCampo('cod_atributo_data'    ,'integer' ,true  ,''     ,false,'TAdministracaoAtributoDinamico','cod_atributo');
        $this->AddCampo('cod_modulo_data'      ,'integer' ,true  ,''     ,false,'TAdministracaoAtributoDinamico','cod_modulo');
        $this->AddCampo('cod_cadastro_data'    ,'integer' ,true  ,''     ,false,'TAdministracaoAtributoDinamico','cod_cadastro');
        $this->AddCampo('cod_atributo_mat'     ,'integer' ,true  ,''     ,false,true);
        $this->AddCampo('cod_modulo_mat'       ,'integer' ,true  ,''     ,false,true);
        $this->AddCampo('cod_cadastro_mat'     ,'integer' ,true  ,''     ,false,true);
        $this->AddCampo('cod_evento_automatico','integer' ,true  ,''     ,false,'TFolhaPagamentoEvento','cod_evento');
        $this->AddCampo('cod_evento_base'      ,'integer' ,true  ,''     ,false,true);
        $this->AddCampo('codigo_orgao'         ,'integer' ,true  ,''     ,false,false);
        $this->AddCampo('contribuicao_pat'     ,'numeric' ,true  ,'5,2'  ,false,false);
        $this->AddCampo('contibuicao_serv'     ,'numeric' ,true  ,'5,2'  ,false,false);
        $this->AddCampo('vigencia'             ,'date'    ,true  ,''     ,true,false);

    }

    public function recuperaTodosVigencia(&$rsRecordSet, $stFiltro = "", $stOrdem ="", $boTransacao = "")
    {
        if (trim($stOrdem)=="") {$stOrdem=" ORDER BY cod_configuracao DESC ";}
        $obErro = $this->executaRecupera("montaRecuperaTodosVigencia",$rsRecordSet,$stFiltro,$stOrdem);

        return $obErro;
    }

    public function montaRecuperaTodosVigencia()
    {
        $stSql = "   SELECT configuracao_ipe.*                                                                              \n";
        $stSql .= "      , configuracao_ipe_pensionista.cod_atributo_data as cod_atributo_data_pen                           \n";
        $stSql .= "      , configuracao_ipe_pensionista.cod_modulo_data as cod_modulo_data_pen                               \n";
        $stSql .= "      , configuracao_ipe_pensionista.cod_cadastro_data as cod_cadastro_data_pen                           \n";
        $stSql .= "      , configuracao_ipe_pensionista.cod_atributo_mat as cod_atributo_mat_pen                             \n";
        $stSql .= "      , configuracao_ipe_pensionista.cod_modulo_mat as cod_modulo_mat_pen                                 \n";
        $stSql .= "      , configuracao_ipe_pensionista.cod_cadastro_mat as cod_cadastro_mat_pen                             \n";
        $stSql .= "     FROM folhapagamento.configuracao_ipe                                       \n";
        $stSql .= "LEFT JOIN folhapagamento.configuracao_ipe_pensionista                           \n";
        $stSql .= "       ON configuracao_ipe.cod_configuracao = configuracao_ipe_pensionista.cod_configuracao               \n";
        $stSql .= "      AND configuracao_ipe.vigencia = configuracao_ipe_pensionista.vigencia                               \n";

        if ($this->getDado("cod_periodo_movimentacao") != "") {
            $stSql .= "  WHERE configuracao_ipe.vigencia <= (SELECT dt_final                                                                  \n";
            $stSql .= "                                        FROM folhapagamento.periodo_movimentacao             \n";
            $stSql .= "                                       WHERE cod_periodo_movimentacao = ".$this->getDado("cod_periodo_movimentacao").")\n";
       }//

       return $stSql;
    }

    public function montaRecuperaVigencia()
    {
        $stSql  = " SELECT configuracao_ipe.cod_configuracao                                           \n";
        $stSql .= "      , to_char(configuracao_ipe.vigencia, 'dd/mm/yyyy') as vigencia                \n";
        $stSql .= "   FROM folhapagamento.configuracao_ipe                   \n";
        $stSql .= "      , (SELECT max(cod_configuracao) as cod_configuracao                           \n";
        $stSql .= "              , vigencia                                                            \n";
        $stSql .= "           FROM folhapagamento.configuracao_ipe           \n";
        $stSql .= "          GROUP BY vigencia) as max_configuracao_ipe                                \n";
        $stSql .= "  WHERE configuracao_ipe.cod_configuracao = max_configuracao_ipe.cod_configuracao   \n";
        $stSql .= "    AND configuracao_ipe.vigencia = max_configuracao_ipe.vigencia                   \n";

        return $stSql;
    }

    public function recuperaVigencia(&$rsRecordSet, $stFiltro = "", $stOrdem ="", $boTransacao = "")
    {
        if (trim($stOrdem) == "") {
            $stOrdem = " ORDER BY configuracao_ipe.vigencia DESC";
        }

        $obErro = $this->executaRecupera("montaRecuperaVigencia",$rsRecordSet,$stFiltro,$stOrdem);

        return $obErro;
    }

    public function montaRecuperaRelacionamento()
    {
        $stSql  = " SELECT configuracao_ipe.*                                                                     \n";
        $stSql .= "      , to_char(configuracao_ipe.vigencia, 'dd/mm/yyyy') as vigencia                           \n";
        $stSql .= "      , configuracao_ipe_pensionista.cod_atributo_data as cod_atributo_data_pen                \n";
        $stSql .= "      , configuracao_ipe_pensionista.cod_modulo_data as cod_modulo_data_pen                    \n";
        $stSql .= "      , configuracao_ipe_pensionista.cod_cadastro_data as cod_cadastro_data_pen                \n";
        $stSql .= "      , configuracao_ipe_pensionista.cod_atributo_mat as cod_atributo_mat_pen                  \n";
        $stSql .= "      , configuracao_ipe_pensionista.cod_modulo_mat as cod_modulo_mat_pen                      \n";
        $stSql .= "      , configuracao_ipe_pensionista.cod_cadastro_mat as cod_cadastro_mat_pen                  \n";
        $stSql .= "   FROM folhapagamento.configuracao_ipe                              \n";
        $stSql .= "   LEFT OUTER JOIN folhapagamento.configuracao_ipe_pensionista       \n";
        $stSql .= "     ON (configuracao_ipe.cod_configuracao = configuracao_ipe_pensionista.cod_configuracao     \n";
        $stSql .= "         AND configuracao_ipe.vigencia = configuracao_ipe_pensionista.vigencia)                \n";

        return $stSql;
    }
}
?>
