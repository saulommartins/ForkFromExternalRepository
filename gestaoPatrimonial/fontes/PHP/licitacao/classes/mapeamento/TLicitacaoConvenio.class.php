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
    * Classe de mapeamento da tabela licitacao.convenio
    * Data de Criação: 15/09/2006

    * @author Analista: Gelson W. Gonçalves
    * @author Desenvolvedor: Nome do Programador

    * @package URBEM
    * @subpackage Mapeamento

    $Id $

    * Casos de uso: uc-03.05.14
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  licitacao.convenio
  * Data de Criação: 15/09/2006

  * @author Analista: Gelson W. Gonçalves
  * @author Desenvolvedor: Nome do Programador

  * @package URBEM
  * @subpackage Mapeamento
*/
class TLicitacaoConvenio extends Persistente
{

    /**
        * Método Construtor
        * @access Private
    */
    public function TLicitacaoConvenio()
    {
        parent::Persistente();
        $this->setTabela("licitacao.convenio");

        $this->setCampoCod('num_convenio');
        $this->setComplementoChave('exercicio');

        $this->AddCampo('num_convenio'          ,'integer' ,false ,''      ,true, false);
        $this->AddCampo('exercicio'             ,'char'    ,false ,'4'     ,true, false);
        $this->AddCampo('cgm_responsavel'       ,'integer' ,false ,''      ,false, true);
        $this->AddCampo('cod_objeto'            ,'integer' ,false ,''      ,false, true);
        $this->AddCampo('cod_tipo_convenio'     ,'integer' ,false ,''      ,false, true);
        $this->AddCampo('cod_documento'         ,'integer' ,false ,''      ,false, true);
        $this->AddCampo('cod_tipo_documento'    ,'integer' ,false ,''      ,false, true);
        $this->AddCampo('observacao'            ,'text'    ,false ,''      ,false, false);
        $this->AddCampo('dt_assinatura'         ,'date'    ,false ,''      ,false, false);
        $this->AddCampo('dt_vigencia'           ,'date'    ,false ,''      ,false, false);
        $this->AddCampo('valor'                 ,'numeric' ,false ,'14,2'  ,false, false);
        $this->AddCampo('fundamentacao'         ,'char'    ,false ,'50  '  ,false, false);
        $this->AddCampo('inicio_execucao'       ,'date'    ,false ,''      ,false, false);
        $this->AddCampo('cod_uf_tipo_convenio'  ,'integer' ,false ,''      ,false, false);
        $this->AddCampo('cod_norma_autorizativa','integer' ,false ,''      ,false, false);

    }

    public function montaRecuperaRelacionamento()
    {
        $stSql  = "    SELECT DISTINCT convenio.*                                           \r\n";
        $stSql .= "         , TO_CHAR(convenio.inicio_execucao, 'dd/mm/yyyy') AS inicio_execucao          \r\n";
        $stSql .= "         , objeto.descricao AS descricao_objeto                          \r\n";
        $stSql .= "         , tipo_convenio.descricao AS descricao_tipo                     \r\n";
        $stSql .= "         , sw_cgm.numcgm                                                 \r\n";
        $stSql .= "         , sw_cgm.nom_cgm                                                \r\n";
        $stSql .= "         , administracao.tipo_documento.descricao                        \r\n";
        $stSql .= "         , CASE                                                          \r\n";
        $stSql .= "              WHEN ( convenio_anulado.num_convenio is not null AND convenio_anulado.dt_anulacao <= now()::date ) then    \r\n";
        $stSql .= "                 'Anulado'                                               \r\n";
        $stSql .= "               ELSE 'Ativo'                                              \r\n";
        $stSql .= "           END AS situacao                                               \r\n";
        $stSql .= "      FROM licitacao.convenio                                            \r\n";
        $stSql .= "INNER JOIN sw_cgm                                                        \r\n";
        $stSql .= "        ON sw_cgm.numcgm = cgm_responsavel                               \r\n";
        $stSql .= "INNER JOIN compras.objeto                                                \r\n";
        $stSql .= "        ON objeto.cod_objeto = convenio.cod_objeto                       \r\n";
        $stSql .= " LEFT JOIN administracao.tipo_documento                                  \r\n";
        $stSql .= "        ON tipo_documento.cod_tipo_documento=convenio.cod_tipo_documento \r\n";
        $stSql .= "INNER JOIN licitacao.tipo_convenio                                       \r\n";
        $stSql .= "        ON tipo_convenio.cod_tipo_convenio = convenio.cod_tipo_convenio  \r\n";
        $stSql .= "       AND tipo_convenio.cod_uf_tipo_convenio::VARCHAR = (SELECT  valor  \r\n";
        $stSql .= "                                                            FROM administracao.configuracao \r\n";
        $stSql .= "                                                           WHERE cod_modulo =2 and parametro='cod_uf' and exercicio = '".Sessao::getExercicio()."') \r\n";
        $stSql .= " LEFT JOIN licitacao.publicacao_convenio                                 \r\n";
        $stSql .= "        ON publicacao_convenio.num_convenio = convenio.num_convenio      \r\n";
        $stSql .= " LEFT JOIN licitacao.participante_convenio                               \r\n";
        $stSql .= "        ON participante_convenio.num_convenio = convenio.num_convenio    \r\n";
        $stSql .= " LEFT JOIN licitacao.convenio_anulado                                    \r\n";
        $stSql .= "        ON convenio_anulado.num_convenio = convenio.num_convenio         \r\n";
        $stSql .= "     WHERE 1 =1                                                          \r\n";
        return $stSql;
    }

    public function recuperaConvenioEsfinge(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        return $this->executaRecupera("montaRecuperaConvenioEsfinge",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function montaRecuperaConvenioEsfinge()
    {
        $stSql = "
        select convenio.num_convenio
            ,case tipo_convenio.cod_tipo_convenio
                when 3 then 9
                else tipo_convenio.cod_tipo_convenio
            end
            ,objeto.descricao as desc_objeto
            ,sw_cgm.nom_cgm as nom_responsavel
            ,to_char(convenio.dt_assinatura, 'dd/mm/yyyy') as dt_assinatura
            ,to_char(convenio.dt_vigencia, 'dd/mm/yyyy') as dt_vigencia
            ,convenio.valor
        from licitacao.convenio
        join licitacao.tipo_convenio
        using (cod_tipo_convenio)
        join compras.objeto
        using (cod_objeto)
        join sw_cgm
        on sw_cgm.numcgm = convenio.cgm_responsavel
        where convenio.dt_assinatura between to_date('".$this->getDado( 'dt_inicial')."','dd/mm/yyyy')
        and to_date('".$this->getDado( 'dt_final')."','dd/mm/yyyy')
        and convenio.exercicio = '".$this->getDado( 'exercicio')."'
        and tipo_convenio.cod_uf_tipo_convenio::VARCHAR = (SELECT  valor  
                                                             FROM administracao.configuracao 
                                                            WHERE cod_modulo =2 and parametro='cod_uf'
                                                              and exercicio = '".$this->getDado( 'exercicio')."' )
        ";

        return $stSql;
    }

    public function recuperaConvenioListagem(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        return $this->executaRecupera("montaConvenioListagem",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function montaConvenioListagem()
    {
        $stSql = "    SELECT convenio.num_convenio                                                \n";
        $stSql .="         , convenio.exercicio                                                   \n";
        $stSql .="         , convenio.cod_objeto                                                  \n";
        $stSql .="         , to_char(convenio.dt_assinatura, 'dd/mm/yyyy') as dt_assinatura        \n";
        $stSql .="         , to_char(convenio.dt_vigencia, 'dd/mm/yyyy') as dt_vigencia           \n";
        $stSql .="         , convenio.valor                                                       \n";
        $stSql .="         , substr(trim(objeto.descricao),1,50) as objeto_descricao              \n";
        $stSql .="         , convenio.cgm_responsavel                                             \n";
        $stSql .="         , sw_cgm.nom_cgm                                                       \n";
        $stSql .="      FROM licitacao.convenio                                                   \n";
        $stSql .="INNER JOIN compras.objeto                                                       \n";
        $stSql .="        ON objeto.cod_objeto = convenio.cod_objeto                              \n";
        $stSql .="INNER JOIN sw_cgm                                                               \n";
        $stSql .="        ON sw_cgm.numcgm = convenio.cgm_responsavel                             \n";
        
        if ( $this->getDado('num_participante') ) {
            $stSql .="INNER JOIN licitacao.participante_convenio                                      \n";
            $stSql .="        ON convenio.num_convenio = participante_convenio.num_convenio           \n";
            $stSql .="       AND convenio.exercicio    = participante_convenio.exercicio              \n";
        }
        
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

        if ( $this->getDado('num_participante') ) {
            $stSql .= " AND participante_convenio.cgm_fornecedor = ".$this->getDado('num_participante')." \n";
        }

        return $stSql;
    }

    public function recuperaConvenioSolicitacao(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        return $this->executaRecupera("montaRecuperaConvenioSolicitacao",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function montaRecuperaConvenioSolicitacao()
    {
        $stSql = " SELECT num_convenio ,
                          exercicio ,
                          cgm_responsavel ,
                          cod_objeto ,
                          cod_tipo_convenio ,
                          cod_documento ,
                          cod_tipo_documento ,
                          observacao ,
                          TO_CHAR(dt_assinatura,'dd/mm/yyyy') AS dt_assinatura ,
                          TO_CHAR(dt_vigencia,'dd/mm/yyyy') AS dt_vigencia ,
                          valor ,
                          fundamentacao ,
                          TO_CHAR(inicio_execucao,'dd/mm/yyyy') AS inicio_execucao
                    FROM
                          licitacao.convenio
                    WHERE (dt_vigencia >= (to_char(now(), 'yyyy-mm-dd'))::date)
                      AND NOT EXISTS (SELECT 1
                                        FROM licitacao.rescisao_convenio
                                        WHERE convenio.exercicio    = rescisao_convenio.exercicio_convenio
                                          AND convenio.num_convenio = rescisao_convenio.num_convenio )
                      AND NOT EXISTS (SELECT 1
                                        FROM licitacao.convenio_anulado
                                       WHERE convenio.exercicio    = convenio_anulado.exercicio
                                         AND convenio.num_convenio = convenio_anulado.num_convenio )
                ";

        return $stSql;
    }

}
