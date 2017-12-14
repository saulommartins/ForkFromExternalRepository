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
    * Classe de mapeamento da tabela ARRECADACAO.DOCUMENTO
    * Data de Criação: 24/05/2007

    * @author Analista: Fabio Bertoldi Rodrigues
    * @author Desenvolvedor: Fernando Piccini Cercato
    * @package URBEM
    * @subpackage Mapeamento

    * $Id: TARRDocumento.class.php 59612 2014-09-02 12:00:51Z gelson $

* Casos de uso: uc-05.03.11
*/

/*
$Log$
Revision 1.1  2007/10/09 18:47:26  cercato
 Ticket#9281#

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

class TARRDocumento extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function TARRDocumento()
    {
        parent::Persistente();
        $this->setTabela('arrecadacao.documento');

        $this->setCampoCod('');
        $this->setComplementoChave('cod_documento,cod_tipo_documento,cod_modelo_documento');

        $this->AddCampo('cod_documento','integer',true,'',true,true);

        $this->AddCampo('descricao','varchar',true,'80',false,false);
        $this->AddCampo('cod_modelo_documento','integer',true,'',false,true);
        $this->AddCampo('cod_tipo_documento','integer',true,'',false,true);
    }

    public function recuperaTipoDocumento(&$rsRecordSet, $stCodDocumento, $stCodTipoDocumento, $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql  = $this->montaRecuperaTipoDocumento( $stCodDocumento, $stCodTipoDocumento );
        // $this->setDebug($stSql);
        #$this->debug(); exit;
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaTipoDocumento($stCodDocumento, $stCodTipoDocumento)
    {
        $stSql =  " SELECT
                        administracao.arquivos_documento.nome_arquivo_swx AS arquivo_odt,
                        administracao.modelo_documento.nome_arquivo_agt AS modelo_documento,
                        administracao.modelo_documento.nome_documento,
                        administracao.modelo_documento.cod_documento

                    FROM
                        administracao.modelo_documento

                    INNER JOIN
                        administracao.modelo_arquivos_documento
                    ON
                        administracao.modelo_arquivos_documento.cod_documento = administracao.modelo_documento.cod_documento
                        AND administracao.modelo_arquivos_documento.cod_tipo_documento = administracao.modelo_documento.cod_tipo_documento

                    INNER JOIN
                        administracao.arquivos_documento
                    ON
                        administracao.arquivos_documento.cod_arquivo = administracao.modelo_arquivos_documento.cod_arquivo

                    WHERE
                        modelo_documento.cod_documento = ".$stCodDocumento." AND modelo_documento.cod_tipo_documento = ".$stCodTipoDocumento;

        return $stSql;
    }

    public function recuperaListaCertidao(&$rsRecordSet, $stFiltro = "", $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql  = $this->montaRecuperaListaCertidao($stFiltro);
        $this->setDebug($stSql);
        //$this->debug(); exit;
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaListaCertidao($stFiltro)
    {
        $stSql =  " SELECT DISTINCT
                        (
                            SELECT
                                sw_cgm_pessoa_fisica.cpf
                            FROM
                                sw_cgm_pessoa_fisica
                            WHERE
                                sw_cgm_pessoa_fisica.numcgm = COALESCE( prop_imovel.numcgm, eco.numcgm, documento_cgm.numcgm )
                        )AS cpf,
                        (
                            SELECT
                                sw_cgm_pessoa_juridica.cnpj
                            FROM
                                sw_cgm_pessoa_juridica
                            WHERE
                                sw_cgm_pessoa_juridica.numcgm = COALESCE( prop_imovel.numcgm, eco.numcgm, documento_cgm.numcgm )
                        )AS cnpj,
                        arrecadacao.fn_consulta_endereco_todos(
                            COALESCE( prop_imovel.numcgm, eco.numcgm, documento_cgm.numcgm ),
                            CASE WHEN prop_imovel.numcgm is not null THEN
                                1
                            ELSE
                                CASE WHEN eco.numcgm IS NOT NULL THEN
                                    2
                                ELSE
                                    3
                                END
                            END,
                            1
                        )AS endereco,
                        arrecadacao.fn_consulta_endereco_todos(
                            COALESCE( prop_imovel.numcgm, eco.numcgm, documento_cgm.numcgm ),
                            CASE WHEN prop_imovel.numcgm is not null THEN
                                1
                            ELSE
                                CASE WHEN eco.numcgm IS NOT NULL THEN
                                    2
                                ELSE
                                    3
                                END
                            END,
                            2
                        )AS bairro,
                        arrecadacao.fn_consulta_endereco_todos(
                            COALESCE( prop_imovel.numcgm, eco.numcgm, documento_cgm.numcgm ),
                            CASE WHEN prop_imovel.numcgm is not null THEN
                                1
                            ELSE
                                CASE WHEN eco.numcgm IS NOT NULL THEN
                                    2
                                ELSE
                                    3
                                END
                            END,
                            3
                        )AS cep,
                        arrecadacao.fn_consulta_endereco_todos(
                            COALESCE( prop_imovel.numcgm, eco.numcgm, documento_cgm.numcgm ),
                            CASE WHEN prop_imovel.numcgm is not null THEN
                                1
                            ELSE
                                CASE WHEN eco.numcgm IS NOT NULL THEN
                                    2
                                ELSE
                                    3
                                END
                            END,
                            4
                        )AS municipio,

                        COALESCE( prop_imovel.numcgm, eco.numcgm, documento_cgm.numcgm ) AS numcgm,
                        (
                            SELECT
                                sw_cgm.nom_cgm
                            FROM
                                sw_cgm
                            WHERE
                                sw_cgm.numcgm = COALESCE( prop_imovel.numcgm, eco.numcgm, documento_cgm.numcgm )
                        )AS contribuinte,
                        CASE WHEN parcelas_vencidas.qtd > 0 THEN
                            6
                        ELSE
                            CASE WHEN parcelas_abertas.qtd > 0 THEN
                                8
                            ELSE
                                7
                            END
                        END AS cod_doc,
                        documento_imovel.inscricao_municipal,
                        documento_empresa.inscricao_economica,
                        lpad(parcela_documento.num_documento::varchar,4,'0'::varchar) as num_documento,
                        parcela_documento.exercicio,
                        documento.cod_documento,
                        documento.cod_tipo_documento,
                        documento.descricao,
                        parcela.cod_lancamento,
                        ( split_part( arrecadacao.fn_busca_origem_lancamento_sem_exercicio( parcela.cod_lancamento, 2, 2 ), '§', 1) ) as cod_credito,
                        ( split_part( arrecadacao.fn_busca_origem_lancamento_sem_exercicio( parcela.cod_lancamento, 2, 2 ), '§', 2) ) as cod_grupo,
                        ( split_part( arrecadacao.fn_busca_origem_lancamento_sem_exercicio( parcela.cod_lancamento, 2, 2 ), '§', 4) ) as exercicio_origem,
                        (
                            split_part( arrecadacao.fn_busca_origem_lancamento_sem_exercicio( parcela.cod_lancamento, 2, 2 ), '§', 3) ||' / '|| split_part( arrecadacao.fn_busca_origem_lancamento_sem_exercicio( parcela.cod_lancamento, 2, 2 ), '§', 4)
                        ) as origem,
                        count( parcela.cod_parcela ) AS qtd_parcelas,
                        SUM( parcela.valor ) AS valor_parcela,
                        to_char( MIN( parcela.vencimento ), 'dd/mm/YYYY' ) AS vencimento,
                        (
                            SELECT
                                to_char( documento_emissao.timestamp, 'dd/mm/YYYY' )
                            FROM
                                arrecadacao.documento_emissao
                            WHERE
                                documento_emissao.cod_documento = documento.cod_documento
                                AND documento_emissao.num_documento = parcela_documento.num_documento
                                AND documento_emissao.exercicio = parcela_documento.exercicio
                        )AS dt_emissao

                    FROM
                        arrecadacao.parcela_documento

                    LEFT JOIN
                        (
                            SELECT
                                count(*) AS qtd,
                                parcela_documento.cod_documento,
                                parcela_documento.num_documento,
                                parcela_documento.exercicio

                            FROM
                                arrecadacao.parcela_documento

                            WHERE
                                parcela_documento.cod_situacao = 2

                            GROUP BY
                                parcela_documento.cod_documento,
                                parcela_documento.num_documento,
                                parcela_documento.exercicio
                        )AS parcelas_vencidas
                    ON
                        parcelas_vencidas.cod_documento = parcela_documento.cod_documento
                        AND parcelas_vencidas.num_documento = parcela_documento.num_documento
                        AND parcelas_vencidas.exercicio = parcela_documento.exercicio

                    LEFT JOIN
                        (
                            SELECT
                                count(*) AS qtd,
                                parcela_documento.cod_documento,
                                parcela_documento.num_documento,
                                parcela_documento.exercicio

                            FROM
                                arrecadacao.parcela_documento

                            WHERE
                                parcela_documento.cod_situacao = 1

                            GROUP BY
                                parcela_documento.cod_documento,
                                parcela_documento.num_documento,
                                parcela_documento.exercicio
                        )AS parcelas_abertas
                    ON
                        parcelas_abertas.cod_documento = parcela_documento.cod_documento
                        AND parcelas_abertas.num_documento = parcela_documento.num_documento
                        AND parcelas_abertas.exercicio = parcela_documento.exercicio

                    INNER JOIN
                        arrecadacao.documento
                    ON
                        documento.cod_documento = parcela_documento.cod_documento

                    LEFT JOIN
                        arrecadacao.documento_imovel
                    ON
                        documento_imovel.num_documento = parcela_documento.num_documento
                        AND documento_imovel.cod_documento = parcela_documento.cod_documento
                        AND documento_imovel.exercicio = parcela_documento.exercicio

                    LEFT JOIN
                        arrecadacao.documento_empresa
                    ON
                        documento_empresa.num_documento = parcela_documento.num_documento
                        AND documento_empresa.cod_documento = parcela_documento.cod_documento
                        AND documento_empresa.exercicio = parcela_documento.exercicio

                    LEFT JOIN
                        arrecadacao.documento_cgm
                    ON
                        documento_cgm.num_documento = parcela_documento.num_documento
                        AND documento_cgm.cod_documento = parcela_documento.cod_documento
                        AND documento_cgm.exercicio = parcela_documento.exercicio

                    LEFT JOIN
                        (
                            SELECT
                                prop.*
                            FROM
                                imobiliario.proprietario AS prop

                            INNER JOIN
                                (
                                    SELECT
                                        inscricao_municipal,
                                        MAX( timestamp) AS timestamp
                                    FROM
                                        imobiliario.proprietario
                                    GROUP BY
                                        inscricao_municipal
                                )AS temp
                            ON
                                temp.inscricao_municipal = prop.inscricao_municipal
                                AND temp.timestamp = prop.timestamp
                        ) AS prop_imovel
                    ON
                        prop_imovel.inscricao_municipal = documento_imovel.inscricao_municipal

                    LEFT JOIN
                        (
                            SELECT DISTINCT
                                COALESCE( cadastro_economico_autonomo.numcgm, cadastro_economico_empresa_fato.numcgm, cadastro_economico_empresa_direito.numcgm ) AS numcgm,
                                cadastro_economico.inscricao_economica

                            FROM
                                economico.cadastro_economico

                            LEFT JOIN
                                economico.cadastro_economico_autonomo
                            ON
                                cadastro_economico_autonomo.inscricao_economica = cadastro_economico.inscricao_economica

                            LEFT JOIN
                                economico.cadastro_economico_empresa_fato
                            ON
                                cadastro_economico_empresa_fato.inscricao_economica = cadastro_economico.inscricao_economica

                            LEFT JOIN
                                economico.cadastro_economico_empresa_direito
                            ON
                                cadastro_economico_empresa_direito.inscricao_economica = cadastro_economico.inscricao_economica
                        ) AS eco
                    ON
                        eco.inscricao_economica = documento_empresa.inscricao_economica

                    INNER JOIN
                        arrecadacao.parcela
                    ON
                        parcela.cod_parcela = parcela_documento.cod_parcela

                    ".$stFiltro."
                    GROUP BY
                        prop_imovel.numcgm,
                        eco.numcgm,
                        documento_cgm.numcgm,
                        documento_imovel.inscricao_municipal,
                        documento_empresa.inscricao_economica,
                        parcela_documento.num_documento,
                        parcela_documento.exercicio,
                        documento.cod_documento,
                        documento.cod_tipo_documento,
                        documento.descricao,
                        parcela.cod_lancamento,
                        parcelas_vencidas.qtd,
                        parcelas_abertas.qtd ";

        return $stSql;
    }

    public function recuperaCapaCertidaoNegativa(&$rsRecordSet, $stNumDocumento, $stExercicio, $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql  = $this->montaRecuperaCapaCertidaoNegativa($stNumDocumento, $stExercicio);
        $this->setDebug($stSql);
        //$this->debug(); exit;
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaCapaCertidaoNegativa($stNumDocumento, $stExercicio)
    {
        $stSql =  " SELECT DISTINCT
                        (
                            SELECT
                                sw_cgm_pessoa_fisica.cpf
                            FROM
                                sw_cgm_pessoa_fisica
                            WHERE
                                sw_cgm_pessoa_fisica.numcgm = COALESCE( prop_imovel.numcgm, eco.numcgm, documento_cgm.numcgm )
                        )AS cpf,
                        (
                            SELECT
                                sw_cgm_pessoa_juridica.cnpj
                            FROM
                                sw_cgm_pessoa_juridica
                            WHERE
                                sw_cgm_pessoa_juridica.numcgm = COALESCE( prop_imovel.numcgm, eco.numcgm, documento_cgm.numcgm )
                        )AS cnpj,
                        arrecadacao.fn_consulta_endereco_todos(
                            COALESCE( prop_imovel.inscricao_municipal, eco.inscricao_economica, documento_cgm.numcgm ),
                            CASE WHEN prop_imovel.inscricao_municipal is not null THEN
                                1
                            ELSE
                                CASE WHEN eco.inscricao_economica IS NOT NULL THEN
                                    2
                                ELSE
                                    3
                                END
                            END,
                            1
                        )AS endereco,
                        arrecadacao.fn_consulta_endereco_todos(
                            COALESCE( prop_imovel.inscricao_municipal, eco.inscricao_economica, documento_cgm.numcgm ),
                            CASE WHEN prop_imovel.inscricao_municipal is not null THEN
                                1
                            ELSE
                                CASE WHEN eco.inscricao_economica IS NOT NULL THEN
                                    2
                                ELSE
                                    3
                                END
                            END,
                            2
                        )AS bairro,
                        arrecadacao.fn_consulta_endereco_todos(
                            COALESCE( prop_imovel.inscricao_municipal, eco.inscricao_economica, documento_cgm.numcgm ),
                            CASE WHEN prop_imovel.inscricao_municipal is not null THEN
                                1
                            ELSE
                                CASE WHEN eco.inscricao_economica IS NOT NULL THEN
                                    2
                                ELSE
                                    3
                                END
                            END,
                            3
                        )AS cep,
                        arrecadacao.fn_consulta_endereco_todos(
                            COALESCE( prop_imovel.inscricao_municipal, eco.inscricao_economica, documento_cgm.numcgm ),
                            CASE WHEN prop_imovel.inscricao_municipal is not null THEN
                                1
                            ELSE
                                CASE WHEN eco.inscricao_economica IS NOT NULL THEN
                                    2
                                ELSE
                                    3
                                END
                            END,
                            4
                        )AS municipio,
                        COALESCE( documento_cgm.numcgm, prop_imovel.numcgm, eco.numcgm ) AS numcgm,
                        (
                            SELECT
                                sw_cgm.nom_cgm
                            FROM
                                sw_cgm
                            WHERE
                                sw_cgm.numcgm = COALESCE( documento_cgm.numcgm, prop_imovel.numcgm, eco.numcgm )
                        )AS contribuinte,
                        documento_imovel.inscricao_municipal,
                        documento_empresa.inscricao_economica,
                        lpad(documento_emissao.num_documento::varchar,4,'0') as num_documento,
                        documento_emissao.exercicio,
                        documento.cod_documento,
                        documento.cod_tipo_documento,
                        documento.descricao,
                        to_char( documento_emissao.timestamp, 'dd/mm/YYYY' ) AS dt_emissao

                    FROM
                        arrecadacao.documento

                    INNER JOIN
                        arrecadacao.documento_emissao
                    ON
                        documento_emissao.cod_documento = documento.cod_documento

                    LEFT JOIN
                        arrecadacao.documento_imovel
                    ON
                        documento_imovel.num_documento = documento_emissao.num_documento
                        AND documento_imovel.cod_documento = documento_emissao.cod_documento
                        AND documento_imovel.exercicio = documento_emissao.exercicio

                    LEFT JOIN
                        arrecadacao.documento_empresa
                    ON
                        documento_empresa.num_documento = documento_emissao.num_documento
                        AND documento_empresa.cod_documento = documento_emissao.cod_documento
                        AND documento_empresa.exercicio = documento_emissao.exercicio

                    LEFT JOIN
                        arrecadacao.documento_cgm
                    ON
                        documento_cgm.num_documento = documento_emissao.num_documento
                        AND documento_cgm.cod_documento = documento_emissao.cod_documento
                        AND documento_cgm.exercicio = documento_emissao.exercicio


                    LEFT JOIN
                        (
                            SELECT
                                prop.*
                            FROM
                                imobiliario.proprietario AS prop

                            INNER JOIN
                                (
                                    SELECT
                                        inscricao_municipal,
                                        MAX( timestamp) AS timestamp
                                    FROM
                                        imobiliario.proprietario
                                    GROUP BY
                                        inscricao_municipal
                                )AS temp
                            ON
                                temp.inscricao_municipal = prop.inscricao_municipal
                                AND temp.timestamp = prop.timestamp
                        ) AS prop_imovel
                    ON
                        prop_imovel.inscricao_municipal = documento_imovel.inscricao_municipal

                    LEFT JOIN
                        (
                            SELECT DISTINCT
                                COALESCE( cadastro_economico_autonomo.numcgm, cadastro_economico_empresa_fato.numcgm, cadastro_economico_empresa_direito.numcgm ) AS numcgm,
                                cadastro_economico.inscricao_economica

                            FROM
                                economico.cadastro_economico

                            LEFT JOIN
                                economico.cadastro_economico_autonomo
                            ON
                                cadastro_economico_autonomo.inscricao_economica = cadastro_economico.inscricao_economica

                            LEFT JOIN
                                economico.cadastro_economico_empresa_fato
                            ON
                                cadastro_economico_empresa_fato.inscricao_economica = cadastro_economico.inscricao_economica

                            LEFT JOIN
                                economico.cadastro_economico_empresa_direito
                            ON
                                cadastro_economico_empresa_direito.inscricao_economica = cadastro_economico.inscricao_economica
                        ) AS eco
                    ON
                        eco.inscricao_economica = documento_empresa.inscricao_economica

                    WHERE
                        documento_emissao.num_documento = ".$stNumDocumento."
                        AND documento_emissao.exercicio = '".$stExercicio."'

                    GROUP BY
                        prop_imovel.numcgm,
                        eco.numcgm,
                        documento_cgm.numcgm,
                        prop_imovel.inscricao_municipal,
                        eco.inscricao_economica,
                        documento_imovel.inscricao_municipal,
                        documento_empresa.inscricao_economica,
                        documento_emissao.num_documento,
                        documento_emissao.exercicio,
                        documento.cod_documento,
                        documento.cod_tipo_documento,
                        documento.descricao,
                        documento_emissao.timestamp ";

        return $stSql;
    }
}
?>
