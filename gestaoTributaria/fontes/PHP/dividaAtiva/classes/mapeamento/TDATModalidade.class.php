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
    * Classe de mapeamento da tabela DIVIDA.MODALIDADE
    * Data de Criação: 22/09/2006

    * @author Analista: Fabio Bertoldi Rodrigues
    * @author Desenvolvedor: Fernando Piccini Cercato
    * @package URBEM
    * @subpackage Mapeamento

    * $Id: TDATModalidade.class.php 63839 2015-10-22 18:08:07Z franver $

* Casos de uso: uc-05.04.07
*/

/*
$Log$
Revision 1.11  2007/09/05 15:58:46  cercato
adicionando acrescimos dinamicos.

Revision 1.10  2007/07/27 15:00:48  cercato
Bug#9767#

Revision 1.9  2007/05/15 15:15:55  cercato
Bug #9264#

Revision 1.8  2007/05/14 15:46:13  cercato
alterando consulta de modalidade para funcionar com modalidades sem reducao e parcelamento.

Revision 1.7  2007/02/09 18:28:24  cercato
correcoes para divida.cobranca

Revision 1.6  2006/10/05 14:59:47  dibueno
Alterações nas colunas da tabela

Revision 1.5  2006/10/05 11:39:38  dibueno
Alterações na função que busca informações sobre a modalidade

Revision 1.4  2006/09/29 11:43:12  dibueno
debug comentado

Revision 1.2  2006/09/29 08:31:20  cercato
correcao do montaListaModalidade.

Revision 1.1  2006/09/25 14:54:56  cercato
classes de mapeamento para funcionamento da modalidade.

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
//include_once    ( CLA_PERSISTENTE );

class TDATModalidade extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function TDATModalidade()
    {
        parent::Persistente();
        $this->setTabela('divida.modalidade');

        $this->setCampoCod('cod_modalidade');
        $this->setComplementoChave('');

        $this->AddCampo('cod_modalidade','integer',true,'',true,false);
        $this->AddCampo('descricao','varchar',true,'80',false,false);
        $this->AddCampo('ultimo_timestamp','timestamp',false,'',false,false);
        $this->AddCampo('ativa','boolean',true,'',false,false);
    }

    public function recuperaListaModalidade(&$rsRecordSet, $stCondicao , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stSql = $this->montaRecuperaListaModalidade().$stCondicao.$stOrdem;
        $this->setDebug( $stSql );
        //$this->debug();
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaListaModalidade()
    {
        $stSql  = " SELECT \n";
        $stSql .= "     dm.cod_modalidade, \n";
        $stSql .= "     dm.descricao, \n";
        $stSql .= "     dmv.cod_forma_inscricao, \n";
        $stSql .= "     to_char(dmv.vigencia_inicial, 'dd/mm/YYYY') AS vigencia_inicial, \n";
        $stSql .= "     to_char(dmv.vigencia_final, 'dd/mm/YYYY') AS vigencia_final, \n";
        $stSql .= "     dmv.cod_funcao, \n";
        $stSql .= "     dmv.cod_biblioteca, \n";
        $stSql .= "     dmv.cod_modulo, \n";
        $stSql .= "     dmv.cod_norma, \n";
        $stSql .= "     dmv.cod_tipo_modalidade,
                        (
                            SELECT
                                descricao
                            FROM
                                divida.tipo_modalidade
                            WHERE
                                tipo_modalidade.cod_tipo_modalidade = dmv.cod_tipo_modalidade
                        )AS descricao_tipo_modalidade, \n";
        $stSql .= "     dmv.timestamp \n";
        $stSql .= " FROM \n";
        $stSql .= "     divida.modalidade AS dm \n";
        $stSql .= " INNER JOIN  \n";
        $stSql .= "     divida.modalidade_vigencia AS dmv \n";
        $stSql .= " ON \n";
        $stSql .= "     dmv.cod_modalidade = dm.cod_modalidade \n";
        $stSql .= "     AND dmv.timestamp = dm.ultimo_timestamp \n";

        return $stSql;
    }

    public function recuperaInfoModalidade(&$rsRecordSet, $stCondicao , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stSql = $this->montaRecuperaInfoModalidade().$stCondicao.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaInfoModalidade()
    {
        $stSql = "	SELECT																	\n";
        $stSql .="		dm.cod_modalidade,													\n";
        $stSql .="		dm.ativa,															\n";
        $stSql .="		dm.descricao,														\n";
        $stSql .="		dtm.descricao as descricao_tipo_modalidade,							\n";
        $stSql .="		dfi.cod_forma_inscricao,											\n";
        $stSql .="		to_char(dmv.vigencia_inicial, 'dd/mm/YYYY') AS vigencia_inicial,	\n";
        $stSql .="		to_char(dmv.vigencia_final, 'dd/mm/YYYY') AS vigencia_final,		\n";
        $stSql .="		dmv.cod_biblioteca,													\n";
        $stSql .="		dmv.cod_modulo,														\n";
        $stSql .="		dmv.cod_norma,														\n";
        $stSql .="		dmv.cod_tipo_modalidade,											\n";
        $stSql .="		dmv.cod_funcao as cod_funcao_vigencia,								\n";
        $stSql .="		dmv.nom_funcao as nom_funcao_vigencia,								\n";
        $stSql .="		dmv.timestamp,														\n";
        $stSql .="		dmr.percentual as percentual_reducao,								\n";
        $stSql .="		dmr.valor as valor_reducao,											\n";
        $stSql .="		dmr.cod_funcao as cod_funcao_reducao,								\n";
        $stSql .="		dmr.nom_funcao as funcao_reducao,									\n";
        $stSql .="		dmp.num_regra as num_regra_parcela,									\n";
        $stSql .="		dmp.vlr_limite_inicial as limite_inicial,							\n";
        $stSql .="		dmp.vlr_limite_final as limite_final,								\n";
        $stSql .="		dmp.qtd_parcela,													\n";
        $stSql .="		dmp.vlr_minimo,														\n";
        $stSql .="		dmd.cod_documento,													\n";
        $stSql .="		dmd.cod_tipo_documento,												\n";
        $stSql .="		dfi.cod_forma_inscricao,											\n";
        $stSql .="		dfi.descricao as descricao_forma_inscricao							\n";

        $stSql .="	FROM																	\n";
        $stSql .="		divida.modalidade AS dm												\n";

        $stSql .="		INNER JOIN															\n";
        $stSql .="		( select															\n";
        $stSql .="			dmv.cod_modalidade,												\n";
        $stSql .="			dmv.timestamp,												 	\n";
        $stSql .="			dmv.cod_forma_inscricao,										\n";
        $stSql .="			dmv.cod_biblioteca,												\n";
        $stSql .="			dmv.cod_modulo,													\n";
        $stSql .="			dmv.cod_norma,													\n";
        $stSql .="			dmv.cod_tipo_modalidade,										\n";
        $stSql .="			dmv.vigencia_inicial,											\n";
        $stSql .="			dmv.vigencia_final,												\n";
        $stSql .="			af.cod_funcao,													\n";
        $stSql .="			af.nom_funcao													\n";
        $stSql .="		  from																\n";
        $stSql .="			divida.modalidade_vigencia AS dmv								\n";
        $stSql .="			INNER JOIN administracao.funcao as af							\n";
        $stSql .="			ON dmv.cod_funcao = af.cod_funcao								\n";
        $stSql .="			AND dmv.cod_modulo = af.cod_modulo								\n";
        $stSql .="			AND dmv.cod_biblioteca = af.cod_biblioteca						\n";
        $stSql .="		) as dmv															\n";
        $stSql .="		ON  dmv.cod_modalidade = dm.cod_modalidade							\n";
        $stSql .="		AND dmv.timestamp = dm.ultimo_timestamp								\n";

        $stSql .="		INNER JOIN divida.tipo_modalidade as dtm							\n";
        $stSql .="		ON dtm.cod_tipo_modalidade = dmv.cod_tipo_modalidade				\n";

        $stSql .="		INNER JOIN divida.forma_inscricao as dfi							\n";
        $stSql .="		ON dfi.cod_forma_inscricao = dmv.cod_forma_inscricao				\n";

        $stSql .="		LEFT JOIN															\n";
        $stSql .="		( select															\n";
        $stSql .="			dmr.cod_modalidade,												\n";
        $stSql .="			dmr.timestamp,													\n";
        $stSql .="			dmr.percentual,													\n";
        $stSql .="			dmr.valor,														\n";
        $stSql .="			af.cod_funcao,													\n";
        $stSql .="			af.nom_funcao													\n";
        $stSql .="		  from																\n";
        $stSql .="			divida.modalidade_reducao as dmr								\n";
        $stSql .="			INNER JOIN administracao.funcao as af							\n";
        $stSql .="			ON dmr.cod_funcao = af.cod_funcao								\n";
        $stSql .="			AND dmr.cod_modulo = af.cod_modulo								\n";
        $stSql .="			AND dmr.cod_biblioteca = af.cod_biblioteca						\n";
        $stSql .="		) as dmr															\n";
        $stSql .="		ON dmr.cod_modalidade = dm.cod_modalidade							\n";
        $stSql .="		and dmr.timestamp = dm.ultimo_timestamp								\n";

        $stSql .="		LEFT JOIN divida.modalidade_parcela as dmp							\n";
        $stSql .="		ON dmp.cod_modalidade = dm.cod_modalidade							\n";
        $stSql .="		AND dmp.timestamp = dm.ultimo_timestamp								\n";

        $stSql .="		INNER JOIN divida.modalidade_documento as dmd						\n";
        $stSql .="		ON dmd.cod_modalidade = dm.cod_modalidade							\n";
        $stSql .="		AND dmd.timestamp = dm.ultimo_timestamp								\n";

        return $stSql;
    }

    public function recuperaModalidadeSelecionada(&$rsRecordSet, $stCondicao , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stSql = $this->montaRecuperaModalidadeSelecionada().$stCondicao.$stOrdem;
        $this->setDebug( $stSql );
        //$this->debug();
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaModalidadeSelecionada()
    {
        $stSql  = " SELECT \n";
        $stSql .= "     dm.cod_modalidade, \n";
        $stSql .= "     dm.descricao, \n";
        $stSql .= "     dmv.cod_forma_inscricao, \n";
        $stSql .= "     to_char(dmv.vigencia_inicial, 'dd/mm/YYYY') AS vigencia_inicial, \n";
        $stSql .= "     to_char(dmv.vigencia_final, 'dd/mm/YYYY') AS vigencia_final, \n";
        $stSql .= "     dmv.cod_funcao, \n";
        $stSql .= "     dmv.cod_biblioteca, \n";
        $stSql .= "     dmv.cod_modulo, \n";
        $stSql .= "     dmv.cod_norma, \n";
        $stSql .= "     dmv.cod_tipo_modalidade, \n";
        $stSql .= "     dmv.timestamp, \n";
        $stSql .= "     dma.cod_tipo, \n";
        $stSql .= "     dma.cod_acrescimo, \n";
        $stSql .= "     dmc.cod_especie, \n";
        $stSql .= "     dmc.cod_genero, \n";
        $stSql .= "     dmc.cod_natureza, \n";
        $stSql .= "     dmc.cod_credito, \n";
        $stSql .= "     dmc.cod_credito||'.'||dmc.cod_especie||'.'||dmc.cod_genero||'.'||dmc.cod_natureza AS credito \n";
        $stSql .= " FROM \n";
        $stSql .= "     divida.modalidade AS dm \n";
        $stSql .= " INNER JOIN  \n";
        $stSql .= "     divida.modalidade_vigencia AS dmv \n";
        $stSql .= " ON \n";
        $stSql .= "     dmv.cod_modalidade = dm.cod_modalidade \n";
        $stSql .= "     AND dmv.timestamp = dm.ultimo_timestamp \n";
        $stSql .= " INNER JOIN  \n";
        $stSql .= "     divida.modalidade_acrescimo AS dma \n";
        $stSql .= " ON \n";
        $stSql .= "     dma.cod_modalidade = dmv.cod_modalidade \n";
        $stSql .= "     AND dma.timestamp = dmv.timestamp \n";
        $stSql .= " INNER JOIN  \n";
        $stSql .= "     divida.modalidade_credito AS dmc \n";
        $stSql .= " ON \n";
        $stSql .= "     dmc.cod_modalidade = dmv.cod_modalidade \n";
        $stSql .= "     AND dmc.timestamp = dmv.timestamp \n";

        return $stSql;
    }

   public function recuperaModalidadeDocumentos(&$rsRecordSet, $stCondicao , $stOrdem = "" , $boTransacao = "")
   {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stSql = $this->montaRecuperaModalidadeDocumentos().$stCondicao.$stOrdem;
        $this->setDebug( $stSql );
        //$this->debug();
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaModalidadeDocumentos()
    {
        $stSql  = " SELECT
                        dmd.cod_documento
                        , dmd.cod_tipo_documento
                        , dm.cod_modalidade
                        , dm.ultimo_timestamp
                        , amd.nome_arquivo_agt
                        , amd.nome_documento
                        , aad.nome_arquivo_swx
                    FROM
                        divida.modalidade AS dm

                    INNER JOIN
                        divida.modalidade_documento AS dmd
                    ON
                        dm.cod_modalidade = dmd.cod_modalidade
                        AND dm.ultimo_timestamp = dmd.timestamp

                    INNER JOIN
                        administracao.modelo_documento AS amd
                    ON
                        amd.cod_documento = dmd.cod_documento
                        AND amd.cod_tipo_documento = dmd.cod_tipo_documento

                    INNER JOIN
                        administracao.modelo_arquivos_documento AS amad
                    ON
                        amad.cod_documento = dmd.cod_documento
                        AND amad.cod_tipo_documento = dmd.cod_tipo_documento
                        AND amad.padrao = true

                    INNER JOIN
                        administracao.arquivos_documento AS aad
                    ON
                        aad.cod_arquivo = amad.cod_arquivo
        \n";

        return $stSql;
    }

   public function ListaAcrescimosDaModalidade(&$rsRecordSet, $stCondicao , $stOrdem = "" , $boTransacao = "")
   {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stSql = $this->montaListaAcrescimosDaModalidade().$stCondicao.$stOrdem;
        $this->setDebug( $stSql );
        //$this->debug();
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaListaAcrescimosDaModalidade()
    {
        $stSql  = " SELECT
                        modalidade_acrescimo.cod_acrescimo,
                        modalidade_acrescimo.cod_tipo,
                        acrescimo.descricao_acrescimo

                    FROM
                        divida.modalidade

                    INNER JOIN
                        divida.modalidade_acrescimo
                    ON
                        modalidade_acrescimo.cod_modalidade = modalidade.cod_modalidade
                        AND modalidade_acrescimo.timestamp = modalidade.ultimo_timestamp

                    INNER JOIN
                        monetario.acrescimo
                    ON
                        acrescimo.cod_acrescimo = modalidade_acrescimo.cod_acrescimo
                        AND acrescimo.cod_tipo = modalidade_acrescimo.cod_tipo \n";

        return $stSql;
    }

}// end of class

?>
