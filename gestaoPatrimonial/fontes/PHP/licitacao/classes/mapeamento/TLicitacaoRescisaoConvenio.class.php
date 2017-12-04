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
    * Data de Criação: 24/10/2007

    * @author Analista: Gelson W. Gonçalves
    * @author Desenvolvedor: Henrique Girardi dos Santos

    * @package URBEM
    * @subpackage

    $Id: TLicitacaoRescisaoConvenio.class.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso : uc-03.05.14
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TLicitacaoRescisaoConvenio extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function TLicitacaoRescisaoConvenio()
    {
        parent::Persistente();
        $this->setTabela("licitacao.rescisao_convenio");

        $this->setCampoCod('');
        $this->setComplementoChave('exercicio_convenio, num_convenio');

        $this->AddCampo( 'exercicio_convenio'    , 'char'    , true, '4'     , true  , true  );
        $this->AddCampo( 'num_convenio'          , 'integer' , true, ''      , true  , true  );
        $this->AddCampo( 'exercicio'             , 'char'    , true, '4'     , false , false );
        $this->AddCampo( 'num_rescisao'          , 'integer' , true, ''      , false , false );
        $this->AddCampo( 'responsavel_juridico'  , 'integer' , true, ''      , false , true  );
        $this->AddCampo( 'dt_rescisao'           , 'date'    , true, ''      , false , false );
        $this->AddCampo( 'vlr_multa'             , 'numeric' , true, '14,2'  , false , false );
        $this->AddCampo( 'vlr_indenizacao'       , 'numeric' , true, '14,2'  , false , false );
        $this->AddCampo( 'motivo'                , 'char'    , true, ''      , false , false );
    }

    /**
    * recuperaMontaRecuperaDadosRescisao
    *
    * Executa o método executaRecupera, onde o 1º parâmetro passado executa o método do sql.
    *
    */
    public function recuperaMontaRecuperaDadosRescisao(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        return $this->executaRecupera("montaRecuperaDadosRescisao",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    /**
    * montaRecuperaDadosRescisao
    *
    * método que monta o sql para retorno os dados da tabela
    * @return string
    */
    public function montaRecuperaDadosRescisao()
    {
        $stSql = " SELECT rescisao_convenio.exercicio_convenio "
                ."\n    , rescisao_convenio.num_convenio"
                ."\n    , rescisao_convenio.exercicio"
                ."\n    , rescisao_convenio.num_rescisao"
                ."\n    , rescisao_convenio.responsavel_juridico"
                ."\n    , sw_cgm.nom_cgm"
                ."\n    , TO_CHAR(rescisao_convenio.dt_rescisao, 'dd/mm/yyyy') as dt_rescisao"
                ."\n    , rescisao_convenio.vlr_multa"
                ."\n    , rescisao_convenio.vlr_indenizacao"
                ."\n    , rescisao_convenio.motivo"
                ."\n FROM licitacao.rescisao_convenio"
                ."\n INNER JOIN sw_cgm"
                ."\n            ON sw_cgm.numcgm = rescisao_convenio.responsavel_juridico"
                ."\n WHERE 1=1 ";

        if ($this->getDado("num_convenio")) {
            $stSql .= " AND rescisao_convenio.num_convenio = ".$this->getDado("num_convenio");
        }

        if ($this->getDado("exercicio_convenio")) {
            $stSql .= " AND rescisao_convenio.exercicio_convenio = '".$this->getDado('exercicio_convenio')."'";
        }

        return $stSql;
    }

    /**
    * recuperaProximoNumConvenio
    *
    * Executa o método executaRecupera, onde o 1º parâmetro passado executa o método do sql.
    *
    */
    public function recuperaProximoNumConvenio(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        return $this->executaRecupera("montaRecuperaProximoNumConvenio",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    /**
    * montaRecuperaProximoNumConvenio
    *
    * método que monta o sql onde retorna o MAX+1 do num_convenio da tabela
    * @return string
    */
    public function montaRecuperaProximoNumConvenio()
    {
        $stSql =   " SELECT COALESCE(MAX(num_rescisao), 0)+1 AS prox_num_rescisao "
                ."\n FROM licitacao.rescisao_convenio"
                ."\n WHERE ";

                if ($this->getDado("num_convenio")) {
                    $stSql .= " rescisao_convenio.num_convenio = ".$this->getDado("num_convenio")." \n AND  ";
                }

                if ($this->getDado("exercicio_convenio")) {
                    $stSql .= " rescisao_convenio.exercicio_convenio = '".$this->getDado('exercicio_convenio')."' \nAND  ";
                }

                $stSql = substr($stSql, 0, strlen($stFiltro)-6);

        return $stSql;
    }
}
