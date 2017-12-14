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
    * Classe de mapeamento da tabela DIVIDA.DIVIDA_ATIVA
    * Data de Criação: 27/09/2006

    * @author Analista: Fabio Bertoldi Rodrigues
    * @author Desenvolvedor: Diego Bueno Coelho
    * @package URBEM
    * @subpackage Mapeamento

    * $Id: TDATDividaAtiva.class.php 66306 2016-08-05 20:19:38Z evandro $

* Casos de uso: uc-05.04.02
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
//include_once    ( CLA_PERSISTENTE );

class TDATDividaAtiva extends Persistente
{
    public $inExercicio    ;
    public $inCodGrupo     ;
    public $inCodCredito   ;
    public $inCodEspecie   ;
    public $inCodGenero    ;
    public $inCodNatureza  ;
    public $inNumCgm       ;
    public $inCodIIInicial ;
    public $inCodIIFinal   ;
    public $inCodIEInicial ;
    public $inCodIEFinal   ;
    public $dtDataInicial  ;
    public $dtDataFinal    ;
    public $flValorInicial ;
    public $flValorFinal   ;

    public $inCodAutoridade;
    public $dtInscricao    ;

    public $stExercicio    ;

    /**
        * Método Construtor
        * @access Private
    */
    public function TDATDividaAtiva()
    {
        parent::Persistente();
        $this->setTabela('divida.divida_ativa');

        $this->setCampoCod('cod_inscricao');
        $this->setComplementoChave('exercicio');

        $this->AddCampo('exercicio'    , 'varchar', true, '4',true, false );
        $this->AddCampo('cod_inscricao', 'integer', true, '' ,true, false );

        $this->AddCampo('cod_autoridade'       ,'integer' , true, '' , false, true  );
        $this->AddCampo('exercicio_original'   ,'varchar' , true, '4', false, true  );
        $this->AddCampo('numcgm_usuario'       ,'integer' , true, '' , false, false );
        $this->AddCampo('dt_inscricao'         ,'date'    , true, '' , false, false );
        $this->AddCampo('num_livro'            ,'integer' , true, '' , false, false );
        $this->AddCampo('num_folha'            ,'integer' , true, '' , false, false );
        $this->AddCampo('dt_vencimento_origem' , 'date'   , true, '' , false, false );
        $this->AddCampo('exercicio_livro'      , 'varchar', true, '' , false, false );

        $this->inExercicio     = '0';
        $this->inCodGrupo      = '0';
        $this->inCodCredito    = '0';
        $this->inCodEspecie    = '0';
        $this->inCodGenero     = '0';
        $this->inCodNatureza   = '0';
        $this->inNumCgmInicial = '0';
        $this->inNumCgmFinal   = '0';
        $this->inCodIIInicial  = '0';
        $this->inCodIIFinal    = '0';
        $this->inCodIEInicial  = '0';
        $this->inCodIEFinal    = '0';
        $this->dtDataInicial   = null;
        $this->dtDataFinal     = null;
        $this->flValorInicial  = null;
        $this->flValorFinal    = null;

        $this->inCodModalidade  = null;
        $this->inCodAutoridade  = null;
        $this->dtInscricao      = null;

        $this->stExercicio      = null;
    }

    public function recuperaListaDivida(&$rsRecordSet, $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stSql = $this->montaRecuperaListaDivida();

        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaListaDivida()
    {
        $stSql  = "   SELECT                                                                      
                                *                                                                       
                            FROM divida.fn_lista_divida_arrecadacao (".$this->inExercicio."
                                                                    ,".$this->inCodGrupo."
                                                                    ,".$this->inCodCredito."
                                                                    ,".$this->inCodEspecie."
                                                                    ,".$this->inCodGenero."
                                                                    ,".$this->inCodNatureza."
                                                                    ,".$this->inNumCgmInicial."
                                                                    ,".$this->inNumCgmFinal."
                                                                    ,".$this->inCodIIInicial."
                                                                    ,".$this->inCodIIFinal."
                                                                    ,".$this->inCodIEInicial."
                                                                    ,".$this->inCodIEFinal."
                                                                    ,'".$this->dtDataInicial."'
                                                                    ,'".$this->dtDataFinal."'
                                                                    ,".$this->flValorInicial."
                                                                    ,".$this->flValorFinal."
                                                                    ,'".$this->stExercicio."'
                            ) as lista_dividas (                                                          
                                            valor_aberto numeric,                                                   
                                            valor_lancamento numeric,                                               
                                            cod_lancamento int,                                                     
                                            numcgm int,                                                             
                                            nom_cgm varchar,                                                        
                                            vinculo varchar,                                                        
                                            id_vinculo varchar,                                                     
                                            inscricao int,                                                          
                                            tipo_inscricao varchar,                                                 
                                            vencimento_base date,                                                   
                                            vencimento_base_br varchar,                                             
                                            timestamp_venal timestamp,                                              
                                            nro_parcelas int,                                                       
                                            situacao_lancamento varchar                                             
                            )                                                                                       
            ";
        return $stSql;

    }

    public function recuperaFolhaPopUp(&$rsRecordSet, $stCondicao, $stOrdem = "", $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stSql = $this->montaRecuperaFolhaPopUp().$stCondicao.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaFolhaPopUp()
    {
        $stSql = " SELECT num_livro,  num_folha \n";
        $stSql .= "   FROM divida.divida_ativa \n";

        return $stSql;
    }

    public function recuperaLivroPopUp(&$rsRecordSet, $stCondicao, $stOrdem = "", $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stSql = $this->montaRecuperaLivroPopUp().$stCondicao.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaLivroPopUp()
    {
        $stSql = " SELECT num_livro, exercicio_livro \n";
        $stSql .= "   FROM divida.divida_ativa \n";

        return $stSql;
    }

    public function recuperaListaDividaPopUP(&$rsRecordSet, $stCondicao , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stSql = $this->montaRecuperaListaDividaPopUP().$stCondicao.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaListaDividaPopUP()
    {
        $stSql  = "

        SELECT DISTINCT dda.*
                                , ddc.numcgm
                                , (
                                        SELECT nom_cgm
                                           FROM  sw_cgm
                                         WHERE numcgm = ddc.numcgm
                                   ) AS nom_cgm
                                , COALESCE(ddi.inscricao_municipal, dde.inscricao_economica)||' - ' AS cod_inscricao_imec
                                , CASE WHEN ddi.inscricao_municipal IS NOT NULL
                                            THEN imobiliario.fn_busca_endereco_imovel_formatado( ddi.inscricao_municipal )
                                             ELSE (
                                                        SELECT (
                                                                        SELECT nom_cgm
                                                                           FROM sw_cgm
                                                                         WHERE numcgm = COALESCE(CEED.numcgm, CEEF.numcgm, CEA.numcgm )
                                                                    )
                                                           FROM   economico.cadastro_economico AS CE
                                                      LEFT JOIN   economico.cadastro_economico_empresa_direito CEED
                                                               ON  CEED.inscricao_economica = CE.inscricao_economica

                                                     LEFT JOIN  economico.cadastro_economico_empresa_fato CEEF
                                                              ON  CEEF.inscricao_economica = CE.inscricao_economica

                                                       LEFT JOIN economico.cadastro_economico_autonomo CEA
                                                                ON  CEA.inscricao_economica = CE.inscricao_economica

                                                          WHERE  CE.inscricao_economica = dde.inscricao_economica
                                                 ) END AS descricao_inscricao_imec
                                                   , (CASE WHEN ddi.cod_inscricao is not null then
                                                          'IM'
                                                     ELSE
                                                          'IE'
                                                      END
                                                   ) as tipo_divida
                                                   , (CASE WHEN ddcanc.cod_inscricao is not null then
                                                          true
                                                      ELSE
                                                           false
                                                     END
                                                   ) as cancelada
                                           FROM divida.divida_ativa AS dda
                                   INNER JOIN  divida.divida_cgm AS ddc
                                               ON ddc.cod_inscricao = dda.cod_inscricao
                                             AND ddc.exercicio = dda.exercicio
                                     LEFT JOIN sw_cgm
                                               ON sw_cgm.numcgm = ddc.numcgm
                                     LEFT JOIN divida.divida_imovel AS ddi
                                               ON  ddi.cod_inscricao = dda.cod_inscricao
                                            AND ddi.exercicio = dda.exercicio
                                    LEFT JOIN divida.divida_empresa AS dde
                                              ON dde.cod_inscricao = dda.cod_inscricao
                                            AND dde.exercicio = dda.exercicio
                                    LEFT JOIN  divida.divida_cancelada AS ddcanc
                                              ON ddcanc.cod_inscricao = dda.cod_inscricao
                                            AND ddcanc.exercicio = dda.exercicio                    ";

        return $stSql;
    }

    public function recuperaCodigoInscricaoComponente(&$rsRecordSet, $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stSql = $this->montaRecuperaCodigoInscricaoComponente();
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaCodigoInscricaoComponente()
    {
        ;

        $stSql  = " SELECT                                                                      \n";
        $stSql .= "     divida.fn_busca_inscricao_divida(".Sessao::getExercicio().") AS max_inscricao                     \n";

        return $stSql;

    }

    public function recuperaCodigoInscricaoComponenteMax(&$rsRecordSet, $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stSql = $this->montaRecuperaCodigoInscricaoComponenteMax();
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaCodigoInscricaoComponenteMax()
    {
        ;

        $stSql  = " SELECT
                        max(cod_inscricao) AS max_inscricao
                    FROM
                        divida.divida_ativa \n";

        return $stSql;

    }

    public function recuperaLivroMax(&$rsRecordSet, $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stSql = $this->montaRecuperaLivroMax();
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }
    public function montaRecuperaLivroMax()
    {
        global $sessao;

        $stSql = " Select max(num_livro) as max_livro FROM divida.divida_ativa \n";

        return $stSql;
    }
    public function recuperaListaParcelasDivida(&$rsRecordSet, $stParametros, $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stSql = $this->montaRecuperaListaParcelasDivida( $stParametros );
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;

    }

    public function montaRecuperaListaParcelasDivida($stParametros)
    {
        $stSql  = "   SELECT                                                          \r\n";
        $stSql .= "       *                                                           \r\n";
        $stSql .= "   FROM divida.fn_recupera_parcelas_divida_lancamento(             \r\n";

        $stSql .=       $stParametros."                                               \r\n";
        $stSql .= "   )                                                               \r\n";

        $stSql .= "   as lista_parcelas_divida (                                      \r\n";
        $stSql .= "       numeracao varchar,                                          \r\n";
        $stSql .= "       cod_convenio int,                                           \r\n";
        $stSql .= "       exercicio int,                                              \r\n";
        $stSql .= "       cod_parcela int,                                            \r\n";
        $stSql .= "       cod_calculo int,                                            \r\n";
        $stSql .= "       cod_lancamento int,                                         \r\n";
        $stSql .= "       nr_parcela int,                                             \r\n";
        $stSql .= "       cod_credito int,                                            \r\n";
        $stSql .= "       descricao_credito varchar,                                  \r\n";
        $stSql .= "       cod_natureza int,                                           \r\n";
        $stSql .= "       cod_genero int,                                             \r\n";
        $stSql .= "       cod_especie int,                                            \r\n";
        $stSql .= "       valor numeric,                                              \r\n";
        $stSql .= "       valor_exato numeric                                         \r\n";
        $stSql .= "   )                                                               \r\n";

        return $stSql;

    }

    ############################################################ VERSAO ANTIGA DO SQL
    /*
    public function recuperaListaCobranca(&$rsRecordSet, $stParametros, $boTransacao = "")
    {
         $obErro      = new Erro;
         $obConexao   = new Conexao;
         $rsRecordSet = new RecordSet;

         $stSql = $this->montaRecuperaListaCobranca().$stParametros;
         $this->setDebug( $stSql );
         $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

         return $obErro;

     }

     function montaRecuperaListaCobranca()
     {
         $stSql   = "SELECT distinct
                         dda.exercicio,
                         COALESCE( ddi.inscricao_municipal, dde.inscricao_economica ) AS inscricao,
                         ( CASE WHEN ddi.inscricao_municipal is not null THEN
                             'imobiliaria'
                             ELSE
                             'economica'
                             END
                         ) as inscricao_tipo,
                         dda.cod_inscricao,
                         ddc.numcgm AS numcgm_contribuinte,
                         (
                             SELECT
                                 nom_cgm
                             FROM
                                 sw_cgm
                             WHERE
                                 sw_cgm.numcgm = ddc.numcgm
                         )AS nom_cgm_contribuinte,
                         ddpar.vlr_parcela,
                         (
                             SELECT
                                 sum(divida.parcela_origem.valor)
                             FROM
                                 divida.parcela_origem
                             WHERE
                                 divida.parcela_origem.num_parcelamento = dp.num_parcelamento
                         )AS vlr_original_parcela,
                         to_char(ddpar.dt_vencimento_parcela, 'dd/mm/yyyy') AS dt_vencimento_parcela,
                         to_char(dda.dt_vencimento_origem, 'dd/mm/yyyy') AS dt_vencimento_original,
                         ddpar.num_parcela,
                         dpo.cod_credito || '.' || dpo.cod_especie  || '.' || dpo.cod_genero  || '.' || dpo.cod_natureza AS credito,
                         split_part ( monetario.fn_busca_mascara_credito( dpo.cod_credito, dpo.cod_especie, dpo.cod_genero, dpo.cod_natureza  ), '§', 1 ) as credito_formatado,
                         split_part ( monetario.fn_busca_mascara_credito( dpo.cod_credito, dpo.cod_especie, dpo.cod_genero, dpo.cod_natureza  ), '§', 6 ) as descricao_credito,
                         (
                             SELECT
                                 count(*)
                             FROM
                                 divida.parcela AS ddpar
                             WHERE
                                 ddpar.num_parcelamento = dp.num_parcelamento
                                 AND ddpar.cancelada = false
                                 AND ddpar.paga = false
                         )AS total_de_parcelas_divida,
                         (
                             SELECT
                                 sum(dpc.vl_credito)
                             FROM
                                 divida.parcela AS ddpar

                             INNER JOIN
                                 divida.parcela_calculo AS dpc
                             ON
                                 dpc.num_parcelamento = ddpar.num_parcelamento
                                 AND dpc.num_parcela = ddpar.num_parcela

                             WHERE
                                 ddpar.num_parcelamento = dp.num_parcelamento
                                 AND ddpar.cancelada = false
                                 AND ddpar.paga = false
                         )AS valor_total_parcelas_divida
                         , dp.num_parcelamento

                     FROM
                         divida.divida_ativa AS dda

                     LEFT JOIN
                         divida.divida_imovel AS ddi
                     ON
                         ddi.cod_inscricao = dda.cod_inscricao
                         AND ddi.exercicio = dda.exercicio

                     LEFT JOIN
                         divida.divida_empresa AS dde
                     ON
                         dde.cod_inscricao = dda.cod_inscricao
                         AND dde.exercicio = dda.exercicio

                     INNER JOIN
                         divida.divida_cgm AS ddc
                     ON
                         ddc.cod_inscricao = dda.cod_inscricao
                         AND ddc.exercicio = dda.exercicio

                     INNER JOIN (
                         SELECT
                             tmp.*
                         FROM
                             divida.divida_parcelamento AS tmp,
                             (
                                 SELECT
                                     MAX(num_parcelamento) AS num_parcelamento,
                                     cod_inscricao,
                                     exercicio
                                 FROM
                                     divida.divida_parcelamento AS tmp2
                                 GROUP BY
                                     cod_inscricao, exercicio
                             )AS tmp2
                         WHERE
                             tmp.num_parcelamento = tmp2.num_parcelamento
                             AND tmp.cod_inscricao = tmp2.cod_inscricao
                             AND tmp.exercicio = tmp2.exercicio
                     ) AS dp
                     ON
                         dp.cod_inscricao = dda.cod_inscricao
                         AND dp.exercicio = dda.exercicio

                     INNER JOIN
                         divida.parcelamento  AS ddp
                     ON
                         ddp.num_parcelamento = dp.num_parcelamento

                     LEFT JOIN
                         divida.parcela AS ddpar
                     ON
                         ddpar.num_parcelamento = dp.num_parcelamento
                         AND ddpar.cancelada = false
                         AND ddpar.paga = false

                     INNER JOIN
                         divida.parcela_origem AS dpo
                     ON
                         dpo.num_parcelamento = dp.num_parcelamento

                     WHERE
                         dda.cod_inscricao not in (
                             SELECT
                                 divida.divida_cancelada.cod_inscricao
                             FROM
                                 divida.divida_cancelada
                             WHERE
                                 divida.divida_cancelada.cod_inscricao = dda.cod_inscricao
                         ) \r\n";

         return $stSql;
     } */
    ############################################################ FIM VERSAO ANTIGA DO SQL

    public function recuperaListaCobranca(&$rsRecordSet, $stParametros, $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stSql = $this->montaRecuperaListaCobranca( $stParametros );
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaListaCobranca($stParametros)
    {
        $stSql   = "
SELECT
    cod_inscricao
    , exercicio
    , inscricao
    , inscricao_tipo
    , numcgm_contribuinte
    , nomcgm_contribuinte
    , parcela_origem.credito_formatado
    , parcela_origem.descricao_credito as credito_descricao
    , ( parcela_origem.credito_formatado ||' - '|| parcela_origem.descricao_credito ) as origem
    , dt_vencimento_origem
    , parcela_qtde.qtde as qtde_parcelas
    , sum(parcela_origem.valor) as valor
FROM
    (
        SELECT
            parcelamento_ultimo.cod_inscricao
            , parcelamento_ultimo.exercicio
            , (
                SELECT
                    min(num_parcelamento) as num_parcelamento
                FROM
                    divida.divida_parcelamento as ddp
                WHERE
                    ddp.cod_inscricao = parcelamento_ultimo.cod_inscricao
            ) as minimo_parcelamento
            , parcelamento_ultimo.inscricao
            , parcelamento_ultimo.inscricao_tipo
            , parcelamento_ultimo.numcgm_contribuinte
            , parcelamento_ultimo.nomcgm_contribuinte
            , parcelamento_ultimo.dt_vencimento_origem

        FROM
        (
            SELECT
                cod_inscricao
                , exercicio
                , num_parcelamento
                , inscricao
                , inscricao_tipo
                , numcgm_contribuinte
                , nomcgm_contribuinte
                , cancelada, paga
                , dt_vencimento_origem
            FROM
            (

                SELECT distinct
                    dda.cod_inscricao
                    , dda.exercicio
                    , dda.dt_vencimento_origem
                    , max (ddp.num_parcelamento) as num_parcelamento
                    , ( COALESCE (
                            ddi.inscricao_municipal, dde.inscricao_economica
                            )
                    ) AS inscricao
                    , ( CASE WHEN ddi.inscricao_municipal is not null THEN
                            'IM'
                        ELSE
                            'IE'
                        END
                    ) as inscricao_tipo
                    , cgm.numcgm AS numcgm_contribuinte
                    , cgm.nom_cgm AS nomcgm_contribuinte
                    , ddpar.cancelada
                    , ddpar.paga

                FROM

                    divida.divida_ativa AS dda

                    LEFT JOIN divida.divida_imovel AS ddi
                    ON ddi.cod_inscricao = dda.cod_inscricao
                    AND ddi.exercicio = dda.exercicio

                    LEFT JOIN divida.divida_empresa AS dde
                    ON dde.cod_inscricao = dda.cod_inscricao
                    AND dde.exercicio = dda.exercicio

                    INNER JOIN divida.divida_cgm AS ddcgm
                    ON ddcgm.cod_inscricao = dda.cod_inscricao
                    AND ddcgm.exercicio = dda.exercicio

                    INNER JOIN sw_cgm as cgm
                    ON cgm.numcgm = ddcgm.numcgm

                    LEFT JOIN divida.divida_cancelada as ddcancelada
                    ON ddcancelada.cod_inscricao = dda.cod_inscricao


                    INNER JOIN divida.divida_parcelamento as ddp
                    ON ddp.cod_inscricao = dda.cod_inscricao
                    AND ddp.exercicio = dda.exercicio

                    INNER JOIN divida.parcelamento  AS dp
                    ON  dp.num_parcelamento = ddp.num_parcelamento

                    LEFT JOIN divida.parcela as ddpar
                    ON ddpar.num_parcelamento = ddp.num_parcelamento


                WHERE

                    ddcancelada IS NULL
                    AND ddpar.paga = FALSE
                    AND ( ( ddpar.cancelada = FALSE ) OR ( ddpar.cancelada = TRUE AND ddpar.paga != TRUE ) )

                    ". $stParametros ."

                GROUP BY
                    dda.cod_inscricao
                    , dda.exercicio, dda.dt_vencimento_origem
                    , ddi.inscricao_municipal, dde.inscricao_economica
                    , cgm.numcgm , cgm.nom_cgm
                    , ddpar.cancelada
                    , ddpar.paga

            ) as parcelamento_atual

            GROUP BY
                cod_inscricao
                , exercicio    , dt_vencimento_origem
                , num_parcelamento
                , inscricao
                , inscricao_tipo
                , numcgm_contribuinte
                , nomcgm_contribuinte
                , cancelada, paga
            ORDER BY
                exercicio
        ) as parcelamento_ultimo

    ) as todos_parcelamentos
    INNER JOIN (

        SELECT
            dpo.valor
            , ( split_part ( monetario.fn_busca_mascara_credito( dpo.cod_credito, dpo.cod_especie, dpo.cod_genero, dpo.cod_natureza  ), '§', 1 ) ) as credito_formatado
            , split_part ( monetario.fn_busca_mascara_credito( dpo.cod_credito, dpo.cod_especie, dpo.cod_genero, dpo.cod_natureza  ), '§', 6 ) as descricao_credito
            , dpo.num_parcelamento
            , ap.cod_lancamento
        FROM
            divida.parcela_origem dpo
            INNER JOIN arrecadacao.parcela as ap
            ON ap.cod_parcela = dpo.cod_parcela
            INNER JOIN divida.parcela
            ON divida.parcela.num_parcela = ap.nr_parcela
            AND divida.parcela.num_parcelamento = (
                SELECT
                    max(divida.divida_parcelamento.num_parcelamento)
                FROM
                    divida.divida_parcelamento
                WHERE
                    divida.divida_parcelamento.cod_inscricao = (
                        SELECT
                            divida.divida_parcelamento.cod_inscricao
                        FROM
                            divida.divida_parcelamento
                        WHERE
                            divida.divida_parcelamento.num_parcelamento = dpo.num_parcelamento
                    )AND divida.divida_parcelamento.exercicio = (
                        SELECT
                            divida.divida_parcelamento.exercicio
                        FROM
                            divida.divida_parcelamento
                        WHERE
                            divida.divida_parcelamento.num_parcelamento = dpo.num_parcelamento
                    )
            )
    ) as parcela_origem
    ON parcela_origem.num_parcelamento = todos_parcelamentos.minimo_parcelamento                \n";

    $stSql .="    INNER JOIN (

        SELECT
            count(ap.cod_parcela) as qtde
            , dpo.num_parcelamento
        FROM
            arrecadacao.parcela as ap
            INNER JOIN (
                SELECT
                    max(cod_credito) as cod_credito
                    , cod_parcela
                    , num_parcelamento
                FROM
                    divida.parcela_origem as dpo
                GROUP BY cod_parcela, num_parcelamento
            ) as dpo
            ON dpo.cod_parcela = ap.cod_parcela
        GROUP BY
            dpo.num_parcelamento
    ) as parcela_qtde
    ON parcela_qtde.num_parcelamento = todos_parcelamentos.minimo_parcelamento          \n";

    $stSql .= "
GROUP BY

    cod_inscricao
    , exercicio
    , inscricao, inscricao_tipo
    , dt_vencimento_origem
    , numcgm_contribuinte, nomcgm_contribuinte
    , parcela_origem.credito_formatado
    , parcela_origem.descricao_credito
    , parcela_qtde.qtde

ORDER BY
    exercicio, credito_formatado
 \r\n";

        return $stSql;

    }

    public function recuperaListaCobrancaDetalhe(&$rsRecordSet, $stParametros, $stParametros1, $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $arListaCreditos = array();
        $inTotalDeCreditos = 0;

       $stSql = $this->montaRecuperaListaCobrancaDetalhe( $stParametros,$stParametros1  );
       $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet1, $stSql, $boTransacao );

        if ( !$obErro->ocorreu() && !$rsRecordSet1->eof() ) {
            $inNumParcelamento = -1;
            while ( !$rsRecordSet1->eof() ) {
                if ( $inNumParcelamento != $rsRecordSet1->getCampo("ultimo_parcelamento") ) {
                    $inNumParcelamento = $rsRecordSet1->getCampo("ultimo_parcelamento");
                    $stSql = $this->montaRecuperaListaCreditosCobrancaSemParcela( $inNumParcelamento );
                    $this->setDebug( $stSql );
                    $obErro = $obConexao->executaSQL( $rsRecordSet2, $stSql, $boTransacao );
                    if ( $obErro->ocorreu() )
                        return $obErro;

                    $arCreditos = $rsRecordSet2->getElementos();
                }

                $inTotalCreditos = count($arCreditos);
                $flValorCreditoRS1 = $rsRecordSet1->getCampo("vlr_original_parcela");
                $boCreditoEliminado = false;
                for ($inX=0; $inX<$inTotalCreditos; $inX++) {
                    if ( $arCreditos[$inX]["credito_formatado"] == $rsRecordSet1->getCampo("credito_formatado") ) {
                        $arCreditoSeparado = explode( ".", $arCreditos[$inX]["credito_formatado"] );

                        $stSql = $this->montaRecuperaListaOriginalCobranca()."
                            WHERE
                                num_parcelamento = ".$inNumParcelamento."
                                AND cod_credito = ".$arCreditoSeparado[0]."
                                AND cod_especie = ".$arCreditoSeparado[1]."
                                AND cod_genero = ".$arCreditoSeparado[2]."
                                AND cod_natureza = ".$arCreditoSeparado[3]."
                                AND cod_parcela < ".$rsRecordSet1->getCampo("cod_parcela_origem");

                        $this->setDebug( $stSql );
                        $obErro = $obConexao->executaSQL( $rsRecordSet3, $stSql, $boTransacao );
                        if ( $obErro->ocorreu() )
                            return $obErro;

                        $stSql = $this->montaRecuperaListaOriginalCobranca()."
                            WHERE
                                num_parcelamento = ".$inNumParcelamento."
                                AND cod_credito = ".$arCreditoSeparado[0]."
                                AND cod_especie = ".$arCreditoSeparado[1]."
                                AND cod_genero = ".$arCreditoSeparado[2]."
                                AND cod_natureza = ".$arCreditoSeparado[3]."
                                AND cod_parcela = ".$rsRecordSet1->getCampo("cod_parcela_origem");
                        $this->setDebug( $stSql );
                        $obErro = $obConexao->executaSQL( $rsRecordSet4, $stSql, $boTransacao );
                        if ( $rsRecordSet3->eof() ) {
                            $flValorCreditosAnteriores = 0.00;
                        } else {
                            $flValorCreditosAnteriores = $rsRecordSet3->getCampo("valor");
                        }

                        if ( $rsRecordSet4->eof() ) {
                            $flValorCreditosAnterior = 0.00;
                        } else {
                            $flValorCreditosAnterior = $rsRecordSet4->getCampo("valor");
                        }

                        if ( number_format( ( $flValorCreditosAnteriores + $flValorCreditosAnterior ), 5 ) - number_format($arCreditos[$inX]["vl_credito"],5) > 0 ) {
                            if ( ($arCreditos[$inX]["vl_credito"] - $flValorCreditosAnteriores ) > 0 ) {
                                $flValorCreditoRS1 = number_format($flValorCreditosAnterior,5) -  number_format( $arCreditos[$inX]["vl_credito"] - $flValorCreditosAnteriores ,5);
                            } else {
                                $flValorCreditoRS1 = $flValorCreditosAnterior;
                            }
                        } else {
                            $boCreditoEliminado = true;
                        }
                        break;
                    }
                }

                if (!$boCreditoEliminado) {
                    $arListaCreditos[$inTotalDeCreditos]["cod_inscricao"] = $rsRecordSet1->getCampo("cod_inscricao");
                    $arListaCreditos[$inTotalDeCreditos]["exercicio"] = $rsRecordSet1->getCampo("exercicio");
                    $arListaCreditos[$inTotalDeCreditos]["inscricao"] = $rsRecordSet1->getCampo("inscricao");
                    $arListaCreditos[$inTotalDeCreditos]["inscricao_tipo"] = $rsRecordSet1->getCampo("inscricao_tipo");
                    $arListaCreditos[$inTotalDeCreditos]["numcgm_contribuinte"] = $rsRecordSet1->getCampo("numcgm_contribuinte");
                    $arListaCreditos[$inTotalDeCreditos]["nomcgm_contribuinte"] = $rsRecordSet1->getCampo("nomcgm_contribuinte");
                    $arListaCreditos[$inTotalDeCreditos]["credito_formatado"] = $rsRecordSet1->getCampo("credito_formatado");
                    $arListaCreditos[$inTotalDeCreditos]["credito_descricao"] = $rsRecordSet1->getCampo("credito_descricao");
                    $arListaCreditos[$inTotalDeCreditos]["origem"] = $rsRecordSet1->getCampo("origem");
                    $arListaCreditos[$inTotalDeCreditos]["grupo_original"] = $rsRecordSet1->getCampo("grupo_original");
                    $arListaCreditos[$inTotalDeCreditos]["dt_vencimento_origem"] = $rsRecordSet1->getCampo("dt_vencimento_origem");
                    $arListaCreditos[$inTotalDeCreditos]["dt_vencimento_origem_br"] = $rsRecordSet1->getCampo("dt_vencimento_origem_br");
                    $arListaCreditos[$inTotalDeCreditos]["total_de_parcelas_divida"] = $rsRecordSet1->getCampo("total_de_parcelas_divida");
                    $arListaCreditos[$inTotalDeCreditos]["cod_lancamento_origem"] = $rsRecordSet1->getCampo("cod_lancamento_origem");
                    $arListaCreditos[$inTotalDeCreditos]["cod_parcela_origem"] = $rsRecordSet1->getCampo("cod_parcela_origem");
                    $arListaCreditos[$inTotalDeCreditos]["vlr_original_parcela"] = $flValorCreditoRS1;
                    $arListaCreditos[$inTotalDeCreditos]["ultimo_parcelamento"] = $rsRecordSet1->getCampo("ultimo_parcelamento");

                    $inTotalDeCreditos++;
                }

                $rsRecordSet1->proximo();
            }

            $rsRecordSet->preenche( $arListaCreditos );
        }

        return $obErro;
    }

    public function montaRecuperaListaOriginalCobranca()
    {
        $stSql = "
            SELECT
                sum(valor) as valor

            FROM
                divida.parcela_origem
        ";

        return $stSql;
    }

    public function montaRecuperaListaCreditosCobranca($inNumParcelamento)
    {
        $stSql   = "SELECT
                        ac.cod_calculo,
                        ( split_part ( monetario.fn_busca_mascara_credito( ac.cod_credito, ac.cod_especie, ac.cod_genero, ac.cod_natureza  ), '§', 1 ) ) as credito_formatado,
                        dpc.vl_credito,
                        dp.num_parcela

                    FROM
                        divida.parcela AS dp

                    INNER JOIN
                        divida.parcela_calculo AS dpc
                    ON
                        dpc.num_parcelamento = dp.num_parcelamento
                        AND dpc.num_parcela = dp.num_parcela

                    INNER JOIN
                        arrecadacao.calculo AS ac
                    ON
                        ac.cod_calculo = dpc.cod_calculo

                    WHERE
                        dp.paga = true AND
                        dp.num_parcelamento = ".$inNumParcelamento."

                    ORDER BY
                        ac.cod_calculo";

        return $stSql;
    }

    public function montaRecuperaListaCreditosCobrancaSemParcela($inNumParcelamento)
    {
        $stSql   = "SELECT
                        ac.cod_calculo,
                        ( split_part ( monetario.fn_busca_mascara_credito( ac.cod_credito, ac.cod_especie, ac.cod_genero, ac.cod_natureza  ), '§', 1 ) ) as credito_formatado,
                        sum( dpc.vl_credito ) AS vl_credito

                    FROM
                        divida.parcela AS dp

                    INNER JOIN
                        divida.parcela_calculo AS dpc
                    ON
                        dpc.num_parcelamento = dp.num_parcelamento
                        AND dpc.num_parcela = dp.num_parcela

                    INNER JOIN
                        arrecadacao.calculo AS ac
                    ON
                        ac.cod_calculo = dpc.cod_calculo

                    WHERE
                        dp.paga = true AND
                        dp.num_parcelamento = ".$inNumParcelamento."

                    GROUP BY
                        ac.cod_calculo,
                        ac.cod_credito,
                        ac.cod_especie,
                        ac.cod_genero,
                        ac.cod_natureza ";

        return $stSql;
    }

        function montaRecuperaListaCreditosCobrancaPorInscricao($inCodInscricao, $stExericio)
        {
        $stSql   = "SELECT
                        ac.cod_calculo,
                        ( split_part ( monetario.fn_busca_mascara_credito( ac.cod_credito, ac.cod_especie, ac.cod_genero, ac.cod_natureza  ), '§', 1 ) ) as credito_formatado,
                        sum( dpc.vl_credito ) AS vl_credito

                    FROM
 DIVIDA.DIVIDA_PARCELAMENTO
INNER JOIN
    divida.parcela AS dp
ON dp.num_parcelamento = DIVIDA_PARCELAMENTO.num_parcelamento
INNER JOIN
                        divida.parcela_calculo AS dpc
                    ON
                        dpc.num_parcelamento = dp.num_parcelamento
                        AND dpc.num_parcela = dp.num_parcela

                    INNER JOIN
                        arrecadacao.calculo AS ac
                    ON
                        ac.cod_calculo = dpc.cod_calculo

                    WHERE
                        dp.paga = true AND
    DIVIDA_PARCELAMENTO.cod_inscricao = $inCodInscricao
   and divida_parcelamento.exercicio = '".$stExericio."'

                    GROUP BY
                        ac.cod_calculo,
                        ac.cod_credito,
                        ac.cod_especie,
                        ac.cod_genero,
                        ac.cod_natureza ";

        return $stSql;
    }

    public function montaRecuperaListaCobrancaDetalhe($stParametros, $stParametros1)
    {
        $stSql   = "
SELECT

    cod_inscricao
    , exercicio
    , inscricao
    , inscricao_tipo
    , numcgm_contribuinte
    , nomcgm_contribuinte
    , parcela_origem.origem AS grupo_original
    , parcela_origem.credito_formatado
    , parcela_origem.descricao_credito as credito_descricao
    , ( parcela_origem.credito_formatado ||' - '|| parcela_origem.descricao_credito ) as origem
    , dt_vencimento_origem
    , to_char ( dt_vencimento_origem, 'dd/mm/YYYY' ) as dt_vencimento_origem_br
    , 1 as total_de_parcelas_divida
    , parcela_origem.cod_lancamento as cod_lancamento_origem
    , parcela_origem.cod_parcela as cod_parcela_origem
    , parcela_origem.valor as vlr_original_parcela
    , ultimo_parcelamento
    , minimo_parcelamento

FROM
    (

        SELECT
            parcelamento_ultimo.cod_inscricao
            , parcelamento_ultimo.exercicio
            , (
                SELECT
                    min(num_parcelamento) as num_parcelamento
                FROM
                    divida.divida_parcelamento as ddp
                WHERE
                    ddp.cod_inscricao = parcelamento_ultimo.cod_inscricao
                    AND ddp.exercicio = parcelamento_ultimo.exercicio
            ) as minimo_parcelamento
            , parcelamento_ultimo.inscricao
            , parcelamento_ultimo.inscricao_tipo
            , parcelamento_ultimo.numcgm_contribuinte
            , parcelamento_ultimo.nomcgm_contribuinte
            , parcelamento_ultimo.dt_vencimento_origem
            , parcelamento_ultimo.num_parcelamento AS ultimo_parcelamento

        FROM
        (
            SELECT
                cod_inscricao
                , exercicio
                , num_parcelamento
                , inscricao
                , inscricao_tipo
                , numcgm_contribuinte
                , nomcgm_contribuinte
                , dt_vencimento_origem
            FROM
            (

                SELECT distinct
                    dda.cod_inscricao
                    , dda.exercicio
                    , dda.dt_vencimento_origem
                    , max (ddp.num_parcelamento) as num_parcelamento
                    , ( COALESCE (
                            ddi.inscricao_municipal, dde.inscricao_economica, cgm.numcgm
                            )
                    ) AS inscricao
                    , ( CASE WHEN ddi.inscricao_municipal is not null THEN
                            'imobiliaria'
                        ELSE
                            CASE WHEN dde.inscricao_economica is not null then
                                'economica'
                            ELSE
                                'cgm'
                            END
                        END
                    ) as inscricao_tipo
                    , cgm.numcgm AS numcgm_contribuinte
                    , cgm.nom_cgm AS nomcgm_contribuinte

                FROM

                    divida.divida_ativa AS dda

                    LEFT JOIN divida.divida_imovel AS ddi
                    ON ddi.cod_inscricao = dda.cod_inscricao
                    AND ddi.exercicio = dda.exercicio

                    LEFT JOIN divida.divida_empresa AS dde
                    ON dde.cod_inscricao = dda.cod_inscricao
                    AND dde.exercicio = dda.exercicio

                    INNER JOIN divida.divida_cgm AS ddcgm
                    ON ddcgm.cod_inscricao = dda.cod_inscricao
                    AND ddcgm.exercicio = dda.exercicio

                    INNER JOIN sw_cgm as cgm
                    ON cgm.numcgm = ddcgm.numcgm

                    LEFT JOIN divida.divida_cancelada as ddcancelada
                    ON ddcancelada.cod_inscricao = dda.cod_inscricao
                    AND ddcancelada.exercicio = dda.exercicio

                    LEFT JOIN divida.divida_remissao as ddremissao
                    ON ddremissao.cod_inscricao = dda.cod_inscricao
                    AND ddremissao.exercicio = dda.exercicio

                    LEFT JOIN divida.divida_estorno as ddestorno
                    ON ddestorno.cod_inscricao = dda.cod_inscricao
                    AND ddestorno.exercicio = dda.exercicio

                    INNER JOIN divida.divida_parcelamento as ddp
                    ON ddp.cod_inscricao = dda.cod_inscricao
                    AND ddp.exercicio = dda.exercicio

                    INNER JOIN divida.parcelamento  AS dp
                    ON  dp.num_parcelamento = ddp.num_parcelamento

                    LEFT JOIN divida.parcela as ddpar
                    ON ddpar.num_parcelamento = ddp.num_parcelamento
                    AND ddpar.paga = FALSE
                    AND ( ( ddpar.cancelada = FALSE ) OR ( ddpar.cancelada = TRUE AND ddpar.paga != TRUE ) )

                WHERE
                    ddestorno.cod_inscricao IS NULL
                    AND ddcancelada.cod_inscricao IS NULL
                    AND ddremissao.cod_inscricao IS NULL
                    ". $stParametros ."

                GROUP BY
                    dda.cod_inscricao
                    , dda.exercicio, dda.dt_vencimento_origem
                    , ddi.inscricao_municipal, dde.inscricao_economica
                    , cgm.numcgm , cgm.nom_cgm

            ) as parcelamento_atual

            GROUP BY
                cod_inscricao
                , exercicio    , dt_vencimento_origem
                , num_parcelamento
                , inscricao
                , inscricao_tipo
                , numcgm_contribuinte
                , nomcgm_contribuinte
            ORDER BY
                exercicio
        ) as parcelamento_ultimo


    ) as todos_parcelamentos
    INNER JOIN (

        SELECT
                    arrecadacao.fn_busca_origem_lancamento_sem_exercicio ( ap.cod_lancamento, 1, 1 ) AS origem
                    , dpo.valor
                    , ( split_part ( monetario.fn_busca_mascara_credito( dpo.cod_credito, dpo.cod_especie, dpo.cod_genero, dpo.cod_natureza  ), '§', 1 ) ) as credito_formatado
                    , split_part ( monetario.fn_busca_mascara_credito( dpo.cod_credito, dpo.cod_especie, dpo.cod_genero, dpo.cod_natureza  ), '§', 6 ) as descricao_credito
                    , dpo.num_parcelamento
                    , ap.cod_lancamento
                    , ap.cod_parcela
                FROM divida.parcela_origem dpo

        INNER JOIN arrecadacao.parcela as ap
                    ON ap.cod_parcela = dpo.cod_parcela

        INNER JOIN divida.parcelamento
                    ON parcelamento.num_parcelamento = dpo.num_parcelamento

        INNER JOIN divida.divida_parcelamento
                    ON divida_parcelamento.num_parcelamento = parcelamento.num_parcelamento

            ". $stParametros1 ."
    ) as parcela_origem
    ON parcela_origem.num_parcelamento = todos_parcelamentos.minimo_parcelamento  \n";

    $stSql .= "

ORDER BY
    ultimo_parcelamento, exercicio

--, cod_lancamento, cod_parcela, credito_formatado
 \r\n";

        return $stSql;

    }

############################################ FIM NOVA FUNCAO ##############

    public function recuperaJurosMulta(&$rsRecordSet, $inCobrancaJudicial, $inCodInscricao, $inExercicio, $inCodModalidade, $inRegistro, $flValor, $dtDataVencimento, $dtDataAtual, $boIncidencia, $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stSql = $this->montaRecuperaJurosMulta( $inCobrancaJudicial, $inCodInscricao, $inExercicio, $inCodModalidade, $inRegistro, $flValor, $dtDataVencimento, $dtDataAtual, $boIncidencia );
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaJurosMulta($inCobrancaJudicial, $inCodInscricao, $inExercicio, $inCodModalidade, $inRegistro, $flValor, $dtDataVencimento, $dtDataAtual, $boIncidencia)
    {
        $stSql   = " SELECT \r\n";
        $stSql  .= "      aplica_acrescimo_modalidade( ".$inCobrancaJudicial.", ".$inCodInscricao.", ".$inExercicio.", ".$inCodModalidade.", 2, ".$inRegistro." ,".$flValor.", '".$dtDataVencimento."', '".$dtDataAtual."', '".$boIncidencia."' ) AS juros \r\n";
        $stSql  .= "    , aplica_acrescimo_modalidade( ".$inCobrancaJudicial.", ".$inCodInscricao.", ".$inExercicio.", ".$inCodModalidade.", 3, ".$inRegistro." ,".$flValor.", '".$dtDataVencimento."', '".$dtDataAtual."', '".$boIncidencia."' ) AS multa \r\n";
        $stSql  .= "    , aplica_acrescimo_modalidade( ".$inCobrancaJudicial.", ".$inCodInscricao.", ".$inExercicio.", ".$inCodModalidade.", 1, ".$inRegistro." ,".$flValor.", '".$dtDataVencimento."', '".$dtDataAtual."', '".$boIncidencia."' ) AS correcao \r\n";

        return $stSql;
    }

    public function verificaUtilizacaoModalidade(&$rsRecordSet, $inCodModalidade, $inRegistro, $dtDataAtual, $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stSql = $this->montaVerificaUtilizacaoModalidade($inCodModalidade, $inRegistro, $dtDataAtual);
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaVerificaUtilizacaoModalidade($inCodModalidade, $inRegistro, $dtDataAtual)
    {
        $stSql   = " SELECT \r\n";
        $stSql  .= "     utilizar_modalidade(".$inCodModalidade.", ".$inRegistro.", '".$dtDataAtual."' ) AS utilizar \r\n";

        return $stSql;
    }

    public function aplicaReducaoModalidade(&$rsRecordSet, $inCodModalidade, $inRegistro, $flValor, $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stSql = $this->montaAplicaReducaoModalidade( $inCodModalidade, $inRegistro, $flValor );
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaAplicaReducaoModalidade($inCodModalidade, $inRegistro, $flValor)
    {
        $stSql   = " SELECT \r\n";
        $stSql  .= "     aplica_reducao_modalidade(".$inCodModalidade.", ".$inRegistro.", ".$flValor." ) AS valor \r\n";

        return $stSql;
    }

    public function aplicaReducaoModalidadeCredito(&$rsRecordSet, $inCodModalidade, $inRegistro, $flValor, $inCodCredito, $inCodEspecie, $inCodGenero, $inCodNatureza, $dtDataVencimento, $inNumeroParcelas, $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stSql = $this->montaAplicaReducaoModalidadeCredito( $inCodModalidade, $inRegistro, $flValor, $inCodCredito, $inCodEspecie, $inCodGenero, $inCodNatureza, $dtDataVencimento, $inNumeroParcelas );
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaAplicaReducaoModalidadeCredito($inCodModalidade, $inRegistro, $flValor, $inCodCredito, $inCodEspecie, $inCodGenero, $inCodNatureza, $dtDataVencimento, $inNumeroParcelas)
    {
        $stSql   = " SELECT \r\n";
        $stSql  .= "     aplica_reducao_modalidade_credito(".$inCodModalidade.", ".$inRegistro.", ".$flValor.", ".$inCodCredito.", ".$inCodEspecie.", ".$inCodGenero.", ".$inCodNatureza.", '".$dtDataVencimento."',". $inNumeroParcelas." ) AS valor \r\n";

        return $stSql;
    }

    public function aplicaReducaoModalidadeAcrescimo(&$rsRecordSet, $inCodModalidade, $inRegistro, $flValor, $inCodAcrescimo, $inCodTipo, $dtDataVencimento, $inNumeroParcelas, $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stSql = $this->montaAplicaReducaoModalidadeAcrescimo( $inCodModalidade, $inRegistro, $flValor, $inCodAcrescimo, $inCodTipo, $dtDataVencimento, $inNumeroParcelas );
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaAplicaReducaoModalidadeAcrescimo($inCodModalidade, $inRegistro, $flValor, $inCodAcrescimo, $inCodTipo, $dtDataVencimento, $inNumeroParcelas)
    {
        $stSql   = " SELECT \r\n";
        $stSql  .= "     aplica_reducao_modalidade_acrescimo(".$inCodModalidade.", ".$inRegistro.", ".$flValor.", ".$inCodAcrescimo.", ".$inCodTipo.", '".$dtDataVencimento."', ".$inNumeroParcelas." ) AS valor \r\n";

        return $stSql;
    }

    ############################################ LISTA CONSULTA DIVIDA ##################
    /**
    /* Função utilizada na LSConsultaInscricao da consulta da DIVIDA
    /* recupera as inscricoes em divida de determinado imovel, empresa ou CGM
    /* Agora, utilizando a ListaConsultaDivida2 no urbem.
    /* Foi obtido uma melhora de custo de 78930.45 para 17385.35 ( 4,5x mais rapido )
    **/

    public function ListaConsultaDivida(&$rsRecordSet, $stParametros, $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stSql = $this->montaListaConsultaDivida().$stParametros;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;

    }

    public function montaListaConsultaDivida()
    {
        $stSql   = "
            SELECT DISTINCT
                t.*
                , CASE WHEN acgc.cod_grupo IS NOT NULL THEN
                    (
                        SELECT
                            agc.cod_grupo || '-' ||agc.descricao
                        FROM
                            arrecadacao.grupo_credito AS agc
                        WHERE
                            agc.cod_grupo = acgc.cod_grupo
                            AND agc.ano_exercicio = acgc.ano_exercicio
                    )
                ELSE
                    ac.cod_credito || '.' || ac.cod_especie  || '.' || ac.cod_genero  || '.' || ac.cod_natureza
                END AS credito
                , (  CASE WHEN ddcanc.cod_inscricao IS NOT NULL THEN
                        'Cancelada'
                        ELSE
                        CASE WHEN ( t.total_parcelas = 0 ) OR (t.total_parcelas_canceladas > 0) THEN
                            'Sem cobrança'
                        ELSE
                            CASE WHEN t.total_parcelas_pagas >= t.total_parcelas THEN
                                'Paga'
                            ELSE
                                'Aberta'
                            END
                        END
                    END
                    ) AS situacao
                , (
                    SELECT
                            dm.descricao
                    FROM
                            divida.modalidade AS dm
                    WHERE
                            dm.cod_modalidade = ddp.cod_modalidade
                )AS modalidade_descricao
                , ddp.cod_modalidade
                , ddproc.cod_processo
                , ddproc.ano_exercicio
            FROM
                (
                    SELECT
                        to_char(dda.dt_inscricao, 'dd/mm/yyyy') AS dt_inscricao_divida,
                        dda.exercicio,
                        (
                            SELECT
                                dp.num_parcelamento
                            FROM
                                divida.divida_parcelamento AS dp
                            WHERE
                                dp.cod_inscricao = dda.cod_inscricao
                                AND dp.exercicio = dda.exercicio
                            ORDER BY dp.num_parcelamento DESC
                            LIMIT 1
                        )AS num_parcelamento,
                        COALESCE( ddi.inscricao_municipal, dde.inscricao_economica ) AS inscricao,
                        ddi.inscricao_municipal,
                        dde.inscricao_economica,
                        dda.cod_inscricao,
                        ddc.numcgm AS numcgm_contribuinte,
                        (
                            SELECT
                                nom_cgm
                            FROM
                                sw_cgm
                            WHERE
                                sw_cgm.numcgm = ddc.numcgm
                        )AS nom_cgm_contribuinte,
                        (
                            SELECT
                                divida.autoridade.numcgm
                            FROM
                                divida.autoridade
                            WHERE
                                divida.autoridade.cod_autoridade = dda.cod_autoridade
                        )AS numcgm_autoridade,

                        (
                            SELECT
                                (
                                    SELECT
                                        nom_cgm
                                    FROM
                                        sw_cgm
                                    WHERE
                                        sw_cgm.numcgm = divida.autoridade.numcgm
                                )
                            FROM
                                divida.autoridade
                            WHERE
                                divida.autoridade.cod_autoridade = dda.cod_autoridade
                        )AS nom_cgm_autoridade,

                        (
                            SELECT
                                count(*)
                            FROM
                                divida.parcela
                            WHERE
                                divida.parcela.num_parcelamento =
                                    (
                                        SELECT
                                            dp.num_parcelamento
                                        FROM
                                            divida.divida_parcelamento AS dp
                                        WHERE
                                            dp.cod_inscricao = dda.cod_inscricao
                                            AND dp.exercicio = dda.exercicio
                                        ORDER BY dp.num_parcelamento DESC
                                        LIMIT 1
                                    )
                        )AS total_parcelas,
                        (
                            SELECT
                                count(*)
                            FROM
                                divida.parcela
                            WHERE
                                divida.parcela.cancelada = true
                                AND divida.parcela.paga = false
                                AND divida.parcela.num_parcelamento =
                                    (
                                        SELECT
                                            dp.num_parcelamento
                                        FROM
                                            divida.divida_parcelamento AS dp
                                        WHERE
                                            dp.cod_inscricao = dda.cod_inscricao
                                            AND dp.exercicio = dda.exercicio
                                        ORDER BY dp.num_parcelamento DESC
                                        LIMIT 1
                                    )
                        )AS total_parcelas_canceladas,
                        (
                            SELECT
                                count(*)
                            FROM
                                divida.parcela
                            WHERE
                                divida.parcela.cancelada = false
                                AND divida.parcela.paga = true
                                AND divida.parcela.num_parcelamento =
                                    (
                                        SELECT
                                            dp.num_parcelamento
                                        FROM
                                            divida.divida_parcelamento AS dp
                                        WHERE
                                            dp.cod_inscricao = dda.cod_inscricao
                                            AND dp.exercicio = dda.exercicio
                                        ORDER BY dp.num_parcelamento DESC
                                        LIMIT 1
                                    )
                        )AS total_parcelas_pagas

                    FROM
                        divida.divida_ativa AS dda

                        LEFT JOIN
                            divida.divida_imovel AS ddi
                        ON
                            ddi.cod_inscricao = dda.cod_inscricao
                            AND ddi.exercicio = dda.exercicio

                        LEFT JOIN
                            divida.divida_empresa AS dde
                        ON
                            dde.cod_inscricao = dda.cod_inscricao
                            AND dde.exercicio = dda.exercicio

                        INNER JOIN
                            divida.divida_cgm AS ddc
                        ON
                            ddc.cod_inscricao = dda.cod_inscricao
                            AND ddc.exercicio = dda.exercicio

                ) AS t

                INNER JOIN
                    divida.parcelamento AS ddp
                ON
                    ddp.num_parcelamento = t.num_parcelamento

                LEFT JOIN
                    divida.parcela AS ddpar
                ON
                    ddpar.num_parcelamento = t.num_parcelamento

                INNER JOIN
                    divida.parcela_origem AS dpo
                ON
                    dpo.num_parcelamento = t.num_parcelamento

                INNER JOIN
                    arrecadacao.parcela AS ap
                ON
                    ap.cod_parcela = dpo.cod_parcela

                INNER JOIN
                    arrecadacao.lancamento_calculo AS alc
                ON
                    alc.cod_lancamento = ap.cod_lancamento

                INNER JOIN
                    arrecadacao.calculo AS ac
                ON
                    ac.cod_calculo = alc.cod_calculo

                LEFT JOIN
                    arrecadacao.calculo_grupo_credito AS acgc
                ON
                    acgc.cod_calculo = ac.cod_calculo

                LEFT JOIN
                    divida.divida_cancelada ddcanc
                ON
                    ddcanc.cod_inscricao = t.cod_inscricao

                LEFT JOIN divida.divida_processo as ddproc
                ON ddproc.cod_inscricao = t.cod_inscricao
                AND ddproc.exercicio = t.exercicio
            \n";

        return $stSql;
    }

    public function ListaConsultaDivida2(&$rsRecordSet, $stParametros, $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stSql = $this->montaListaConsultaDivida2($stParametros);
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;

    }

    public function montaListaConsultaDivida2($stParametros)
    {
        $stSql   = "
SELECT
 busca.cod_inscricao,
    busca.exercicio,
    busca.exercicio_original,
    busca.dt_inscricao_divida,
    busca.num_parcelamento,
    busca.inscricao,
    busca.num_livro,
    busca.num_folha,
    busca.inscricao_municipal,
    busca.inscricao_economica,
    busca.nom_cgm_autoridade,
    busca.numcgm_autoridade,
    busca.numcgm_contribuinte,
    busca.nom_cgm_contribuinte,
    arrecadacao.fn_busca_origem_inscricao_divida_ativa ( busca.cod_inscricao, busca.exercicio::integer, 6 ) AS credito,
    busca.cod_processo,
    busca.ano_exercicio,
    busca.total_parcelas,
    busca.total_parcelas_unicas,
    busca.total_parcelas_canceladas,
    busca.total_parcelas_pagas,
    busca.total_parcelas_unicas_pagas,
    busca.inscricao_cancelada,
    busca.numcgm_cancelada,
    busca.usuario_cancelada,
    busca.data_cancelada,
    busca.inscricao_estornada
    , ( CASE WHEN inscricao_cancelada IS NOT NULL THEN
            'Cancelada'
        WHEN inscricao_estornada IS NOT NULL THEN
            'Estornada'
        WHEN inscricao_remida IS NOT NULL THEN
            'Remida'
        WHEN ( total_parcelas = 0 ) OR (total_parcelas_canceladas > 0) THEN
            'Sem cobrança'
        WHEN judicial = TRUE THEN
            'Cobrança Judicial'
        WHEN (( total_parcelas_pagas >= total_parcelas ) OR (total_parcelas_unicas_pagas > 0) ) THEN
            'Paga'
        ELSE
            'Aberta'
        END
    ) AS situacao,
    ( CASE WHEN inscricao_cancelada IS NOT NULL THEN
           motivo_cancelada
        WHEN inscricao_estornada IS NOT NULL THEN
           motivo_estornada
        ELSE
           null
        END
    ) as motivo,
    CASE WHEN ( total_parcelas = 0 ) OR (total_parcelas_canceladas > 0) THEN
        (
            SELECT
                (
                    SELECT
                        modalidade.descricao
                    FROM
                        divida.modalidade
                    WHERE
                        modalidade.cod_modalidade = parcelamento.cod_modalidade
                    ORDER BY
                        timestamp DESC
                    LIMIT 1
                )

            FROM
                divida.divida_parcelamento

            INNER JOIN
                divida.parcelamento
            ON
                parcelamento.num_parcelamento = divida_parcelamento.num_parcelamento

            WHERE
                divida_parcelamento.cod_inscricao = busca.cod_inscricao
                AND divida_parcelamento.exercicio = busca.exercicio

            ORDER BY
                divida_parcelamento.num_parcelamento ASC
            LIMIT 1
        )
    ELSE
        busca.modalidade_descricao
    END AS modalidade_descricao,

    CASE WHEN ( total_parcelas = 0 ) OR (total_parcelas_canceladas > 0) THEN
        (
            SELECT
                parcelamento.cod_modalidade
            FROM
                divida.divida_parcelamento

            INNER JOIN
                divida.parcelamento
            ON
                parcelamento.num_parcelamento = divida_parcelamento.num_parcelamento

            WHERE
                divida_parcelamento.cod_inscricao = busca.cod_inscricao
                AND divida_parcelamento.exercicio = busca.exercicio

            ORDER BY
                divida_parcelamento.num_parcelamento ASC
            LIMIT 1
        )
    ELSE
        busca.cod_modalidade::integer
    END AS cod_modalidade
    , ( CASE WHEN ( total_parcelas = 0 ) OR (total_parcelas_canceladas > 0) THEN
            ' &nbsp;' --sem cobrança
        ELSE
            (
                SELECT
                    (dp2.numero_parcelamento ||'/'||dp2.exercicio)
                from
                    divida.divida_parcelamento as ddp2
                    INNER JOIN divida.parcelamento as dp2
                    ON ddp2.num_parcelamento = dp2.num_parcelamento
                WHERE
                    ddp2.exercicio = busca.exercicio
                    AND ddp2.cod_inscricao = busca.cod_inscricao
                ORDER BY dp2.exercicio DESC, dp2.numero_parcelamento DESC
                LIMIT 1
            )
        END
    ) as max_cobranca,
    busca.remissao_norma,
    busca.remissao_cod_norma
    , to_char(( SELECT MIN(dt_vencimento_parcela) FROM divida.parcela WHERE num_parcelamento = busca.num_parcelamento ), 'dd/mm/yyyy') AS dt_vencimento_parcela

FROM
    (
        SELECT DISTINCT
            dda.*
            , dmod.descricao as modalidade_descricao
            , ddp.cod_modalidade
            , ddproc.cod_processo
            , ddproc.ano_exercicio
            , ddp.judicial
            , (
                SELECT
                    count(*)
                FROM
                    divida.parcela
                WHERE
                    divida.parcela.num_parcelamento = dda.num_parcelamento
                    AND divida.parcela.num_parcela > 0
            ) AS total_parcelas
            ,(
                SELECT
                    count(*)
                FROM
                    divida.parcela
                WHERE
                    divida.parcela.num_parcelamento = dda.num_parcelamento
                    AND divida.parcela.num_parcela = 0
            ) AS total_parcelas_unicas
            , (
                SELECT
                    count(*)
                FROM
                    divida.parcela
                WHERE
                    divida.parcela.cancelada = true
                    AND divida.parcela.paga = false
                    AND divida.parcela.num_parcelamento = dda.num_parcelamento
            ) AS total_parcelas_canceladas
            , (
                SELECT
                    count(*)
                FROM
                    divida.parcela
                WHERE
                    divida.parcela.cancelada = false
                    AND divida.parcela.paga = true
                    AND divida.parcela.num_parcelamento = dda.num_parcelamento
                    AND divida.parcela.num_parcela > 0
            ) AS total_parcelas_pagas
            , (
                SELECT
                    count(*)
                FROM
                    divida.parcela
                WHERE
                    divida.parcela.cancelada = false
                    AND divida.parcela.paga = true
                    AND divida.parcela.num_parcelamento = dda.num_parcelamento
                    AND divida.parcela.num_parcela = 0
            ) AS total_parcelas_unicas_pagas
            , ddcanc.cod_inscricao as inscricao_cancelada
            , ddcanc.motivo as motivo_cancelada
            , ddcanc.numcgm as numcgm_cancelada
            , ( select nom_cgm from sw_cgm where numcgm = ddcanc.numcgm ) as usuario_cancelada
            , ddcanc.timestamp as data_cancelada
            , ddestorn.cod_inscricao as inscricao_estornada
            , ddestorn.motivo as motivo_estornada
            , ddrem.cod_inscricao as inscricao_remida
            , (
                SELECT
                    norma.nom_norma
                FROM
                    normas.norma
                WHERE
                    norma.cod_norma = ddrem.cod_norma
            )AS remissao_norma,
            ddrem.cod_norma AS remissao_cod_norma

        FROM

            (
                SELECT
                    dda.cod_inscricao
                    , dda.exercicio
                    , dda.exercicio_original
                    , dda.num_livro
                    , dda.num_folha
                    , to_char(dda.dt_inscricao, 'dd/mm/yyyy') AS dt_inscricao_divida
                    , max(num_parcelamento) as num_parcelamento

                    , COALESCE( ddi.inscricao_municipal, dde.inscricao_economica ) AS inscricao

                    , ddi.inscricao_municipal
                    , dde.inscricao_economica

                    , autoridade.nom_cgm as nom_cgm_autoridade
                    , autoridade.numcgm as numcgm_autoridade
                    , cgm.numcgm AS numcgm_contribuinte
                    , cgm.nom_cgm AS nom_cgm_contribuinte

                FROM

                    divida.divida_ativa AS dda

                    INNER JOIN divida.divida_parcelamento AS dp
                    ON dp.cod_inscricao = dda.cod_inscricao
                    AND dp.exercicio = dda.exercicio

                    INNER JOIN (
                        SELECT
                            dauto.cod_autoridade
                            , cgm.numcgm
                            , cgm.nom_cgm
                        FROM
                            divida.autoridade as dauto
                            INNER JOIN sw_cgm as cgm
                            ON cgm.numcgm = dauto.numcgm

                    ) as autoridade
                    ON autoridade.cod_autoridade = dda.cod_autoridade

                    LEFT JOIN divida.divida_imovel AS ddi
                    ON ddi.cod_inscricao = dda.cod_inscricao
                    AND ddi.exercicio = dda.exercicio

                    LEFT JOIN divida.divida_empresa AS dde
                    ON dde.cod_inscricao = dda.cod_inscricao
                    AND dde.exercicio = dda.exercicio

                    INNER JOIN divida.divida_cgm AS ddc
                    ON ddc.cod_inscricao = dda.cod_inscricao
                    AND ddc.exercicio = dda.exercicio

                    INNER JOIN sw_cgm as cgm
                    ON cgm.numcgm = ddc.numcgm

                GROUP BY
                    dda.cod_inscricao, dda.exercicio, dda.dt_inscricao
                    , ddi.inscricao_municipal, dde.inscricao_economica
                    , autoridade.nom_cgm, autoridade.numcgm, dda.exercicio_original
                    , cgm.numcgm, cgm.nom_cgm, dda.num_livro, dda.num_folha

            ) AS dda

            INNER JOIN  divida.parcelamento AS ddp
            ON ddp.num_parcelamento = dda.num_parcelamento

            INNER JOIN  (
                SELECT
                    dmod.cod_modalidade
                    , dmod.descricao
                    , max(ultimo_timestamp) as timestamp
                FROM
                    divida.modalidade as dmod
                GROUP BY dmod.cod_modalidade, dmod.descricao
            ) as dmod
            ON dmod.cod_modalidade = ddp.cod_modalidade

            LEFT JOIN divida.parcela AS ddpar
            ON ddpar.num_parcelamento = dda.num_parcelamento

            INNER JOIN divida.parcela_origem AS dpo
            ON dpo.num_parcelamento = dda.num_parcelamento

            INNER JOIN arrecadacao.parcela AS ap
            ON ap.cod_parcela = dpo.cod_parcela

            LEFT JOIN divida.divida_cancelada ddcanc
            ON ddcanc.cod_inscricao = dda.cod_inscricao
               AND ddcanc.exercicio = dda.exercicio

            LEFT JOIN divida.divida_remissao ddrem
            ON ddrem.cod_inscricao = dda.cod_inscricao
               AND ddrem.exercicio = dda.exercicio

            LEFT JOIN divida.divida_estorno ddestorn
            ON ddestorn.cod_inscricao = dda.cod_inscricao
               AND ddestorn.exercicio = dda.exercicio

            LEFT JOIN divida.divida_processo as ddproc
            ON ddproc.cod_inscricao = dda.cod_inscricao
            AND ddproc.exercicio = dda.exercicio

        ". $stParametros ."

    ) as busca
            \n";

        return $stSql;
    }

    public function ListaConsultaLancamentos(&$rsRecordSet, $stParametros, $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stSql = $this->montaListaConsultaLancamentos($stParametros);
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaListaConsultaLancamentos($stParametros)
    {
        $arDataBase = explode( "/",$this->getDado('data_base') );
        $stDataBase = $arDataBase[2]."-".$arDataBase[1]."-".$arDataBase[0];

        $stSql   = "SELECT
                        *,
                        '".$this->getDado('data_base')."' as data_base
                        ,to_number( aplica_acrescimo_modalidade(cod_modalidade,1, CONSULTA.num_parcelamento, valor_lancado, CONSULTA.dt_vencimento_origem, '".$stDataBase."' ), '9999999999.99') +
                        to_number( aplica_acrescimo_modalidade(cod_modalidade,2, CONSULTA.num_parcelamento, valor_lancado, CONSULTA.dt_vencimento_origem, '".$stDataBase."' ), '9999999999.99') +
                        to_number( aplica_acrescimo_modalidade(cod_modalidade,3, CONSULTA.num_parcelamento, valor_lancado, CONSULTA.dt_vencimento_origem, '".$stDataBase."' ), '9999999999.99') + valor_lancado as valor_atualizado
                    FROM (
                        SELECT distinct
                            dda.dt_vencimento_origem,
                            lista_inscricao_imob_eco_cgm_por_num_parcelamento( dp.num_parcelamento ) AS inscricao,
                            CASE WHEN acgc.cod_grupo IS NULL THEN
                                ac.cod_credito || '.' || ac.cod_especie  || '.' || ac.cod_genero  || '.' || ac.cod_natureza
                            ELSE
                                acgc.cod_grupo || '/' || acgc.ano_exercicio || '-' || agc.descricao
                            END AS credito,
                            ac.exercicio,
                            dp.num_parcelamento,
                            dp.cod_modalidade,
                            (
                                SELECT
                                    sum (ac.valor)
                                FROM
                                    arrecadacao.calculo AS ac
                                WHERE
                                    ac.cod_calculo in (
                                        SELECT
                                            cod_calculo
                                        FROM
                                            arrecadacao.lancamento_calculo
                                        WHERE
                                            arrecadacao.lancamento_calculo.cod_lancamento = ap.cod_lancamento
                                    )
                            )AS valor_lancado,
                            (
                                SELECT
                                    total_parcelas
                                FROM
                                    arrecadacao.lancamento
                                WHERE
                                    arrecadacao.lancamento.cod_lancamento = ap.cod_lancamento
                            )AS qtd_parcelas,
                            ap.cod_lancamento
                        FROM
                            divida.parcelamento AS dp

                        INNER JOIN
                            divida.divida_parcelamento AS ddp
                        ON
                            ddp.num_parcelamento = dp.num_parcelamento

                        INNER JOIN
                            divida.divida_ativa AS dda
                        ON
                            dda.cod_inscricao = ddp.cod_inscricao
                            AND dda.exercicio = ddp.exercicio

                        INNER JOIN
                            divida.divida_cgm AS ddc
                        ON
                            ddc.cod_inscricao = ddp.cod_inscricao
                            AND ddc.exercicio = ddp.exercicio

                        INNER JOIN
                            divida.parcela_origem AS dpo
                        ON
                            dpo.num_parcelamento = dp.num_parcelamento

                        INNER JOIN
                            arrecadacao.parcela AS ap
                        ON
                            ap.cod_parcela = dpo.cod_parcela

                        INNER JOIN
                            arrecadacao.lancamento_calculo AS alc
                        ON
                            alc.cod_lancamento = ap.cod_lancamento

                        INNER JOIN
                            arrecadacao.calculo AS ac
                        ON
                            ac.cod_calculo = alc.cod_calculo

                        LEFT JOIN
                            arrecadacao.calculo_grupo_credito AS acgc
                        ON
                            acgc.cod_calculo = ac.cod_calculo

                        LEFT JOIN
                            arrecadacao.grupo_credito AS agc
                        ON
                            agc.cod_grupo = acgc.cod_grupo
                            AND agc.ano_exercicio = acgc.ano_exercicio
                            ".$stParametros."
                ) AS CONSULTA
                \n";

        return $stSql;
    }

    public function listaConsultaLancamentosSimples(&$rsRecordSet, $stParametros, $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stSql = $this->montaListaConsultaLancamentosSimples($stParametros);
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaListaConsultaLancamentosSimples($stParametros)
    {
        $arDataBase = explode( "/",$this->getDado('data_base') );
        $stDataBase = $arDataBase[2]."-".$arDataBase[1]."-".$arDataBase[0];

        $stSql = "  SELECT                                                                              \n";
        $stSql .="      cod_lancamento                                                                  \n";
        $stSql .="      , '".$this->getDado('data_base')."' as data_base                                \n";
        $stSql .="      , origem                                                                        \n";
        $stSql .="      ,  split_part ( arrecadacao.fn_busca_origem_lancamento_sem_exercicio( cod_lancamento, 2, 0), '§', 3) AS nom_origem                                                               \n";
        $stSql .="      , total_parcelas                                                                \n";
        $stSql .="      ,  split_part ( arrecadacao.fn_busca_origem_lancamento_sem_exercicio( cod_lancamento, 2, 0), '§', 4) AS exercicio_original                                                               \n";
        $stSql .="      , sum ( parcela_valor ) as valor_lancado                                        \n";
//        $stSql .="      , sum ( parcela_valor + juros + multa + correcao ) as valor_atualizado          \n";
        $stSql .="       , ( (sum ( parcela_valor) + juros + multa + correcao ) - ( COALESCE( valor_reducao, 0.00) * (sum ( parcela_valor) + juros + multa + correcao ) / COALESCE( acrescimo + calculo , 1 ) ) )::numeric(12,2) AS valor_atualizado \n";

        $stSql .="  FROM                                                                                \n";

        $stSql .="      (                                                                               \n";
        $stSql .="          SELECT                                                                      \n";
        $stSql .="              inscricao.*                                                             \n";
        $stSql .="              , parcelamento.num_parcelamento                                         \n";
        $stSql .="              , ( SELECT sum(valor) FROM divida.parcela_reducao WHERE divida.parcela_reducao.num_parcelamento = parcelamento.num_parcelamento ) as valor_reducao \n";
        $stSql .="      , (select sum(vlracrescimo) from divida.parcela_acrescimo where num_parcelamento = parcelamento.num_parcelamento) AS acrescimo \n";
        $stSql .="              , (select sum(vl_credito) from divida.parcela_calculo where num_parcelamento = parcelamento.num_parcelamento) AS calculo \n";
        $stSql .="              , vorigparcela.valor_origem as parcela_valor                            \n";
        $stSql .="              , vorig.valor_origem as origem                                          \n";
        $stSql .="              , lancs.cod_lancamento  \n";
        $stSql .="              , arrecadacao.fn_total_parcelas_aberto( lancs.cod_lancamento, inscricao.exercicio_original ) as total_parcelas    \n";
        $stSql .="              , ( to_number ( aplica_acrescimo_modalidade( 0, inscricao.cod_inscricao, inscricao.exercicio::integer, cod_modalidade,1           \n";
        $stSql .="                  , dp.num_parcelamento, vorig.valor_origem, vencimento_origem              \n";
        $stSql .="                  , '".$stDataBase."', 'false' ), '9999999999.99' )                            \n";
        $stSql .="              ) as correcao                                                           \n";
        $stSql .="              , ( to_number ( aplica_acrescimo_modalidade( 0, inscricao.cod_inscricao, inscricao.exercicio::integer, cod_modalidade,2           \n";
        $stSql .="                  , dp.num_parcelamento, vorig.valor_origem, vencimento_origem              \n";
        $stSql .="                  , '".$stDataBase."', 'false' ), '9999999999.99' )                            \n";
        $stSql .="              ) as juros                                                              \n";
        $stSql .="              , ( to_number ( aplica_acrescimo_modalidade( 0, inscricao.cod_inscricao, inscricao.exercicio::integer, cod_modalidade,3           \n";
        $stSql .="                  , dp.num_parcelamento, vorig.valor_origem, vencimento_origem              \n";
        $stSql .="                  , '".$stDataBase."', 'false' ), '9999999999.99' )                            \n";
        $stSql .="              ) as multa                                                              \n";

        $stSql .="          FROM                                                                        \n";

        $stSql .="              (                                                                       \n";
        $stSql .="                  SELECT                                                              \n";
        $stSql .="                      dda.cod_inscricao                                               \n";
        $stSql .="                      , dda.exercicio                                                 \n";
        $stSql .="                      , MAX(ddp.num_parcelamento) as num_parcelamento                 \n";
        $stSql .="                      , dda.dt_vencimento_origem as vencimento_origem                 \n";
        $stSql .="                      , dda.exercicio_original                                        \n";
        $stSql .="                      , dda.dt_inscricao                                              \n";
        $stSql .="                  FROM                                                                \n";
        $stSql .="                      divida.divida_ativa AS dda                                      \n";
        $stSql .="                      INNER JOIN divida.divida_parcelamento AS ddp                    \n";
        $stSql .="                      ON ddp.cod_inscricao = dda.cod_inscricao                        \n";
        $stSql .="                      AND ddp.exercicio = dda.exercicio                               \n";
        $stSql .="                  GROUP BY                                                            \n";
        $stSql .="                      dda.cod_inscricao, dda.exercicio, dda.dt_inscricao              \n";
        $stSql .="                      , dda.dt_vencimento_origem , dda.exercicio_original             \n";
        $stSql .="              ) as inscricao                                                          \n";

        $stSql .="              INNER JOIN divida.parcelamento AS dp                                    \n";
        $stSql .="              ON dp.num_parcelamento = inscricao.num_parcelamento                     \n";

        $stSql .="  INNER JOIN (
                                    SELECT
                                        divida_parcelamento.cod_inscricao,
                                        divida_parcelamento.exercicio,
                                        max(divida_parcelamento.num_parcelamento) AS num_parcelamento
                                    FROM
                                        divida.divida_parcelamento
                                    GROUP BY
                                        divida_parcelamento.cod_inscricao,
                                        divida_parcelamento.exercicio
                               )AS parcelamento
                            ON
                                parcelamento.cod_inscricao = inscricao.cod_inscricao
                            AND parcelamento.exercicio = inscricao.exercicio";
        $stSql .="              INNER JOIN
                                    (
                                        SELECT DISTINCT
                                            parcela.cod_lancamento,
                                            parcela_origem.num_parcelamento
                                        FROM
                                            arrecadacao.parcela
                                        INNER JOIN
                                            divida.parcela_origem
                                        ON
                                            parcela_origem.cod_parcela = parcela.cod_parcela
                                    )AS lancs
                                ON
                                    lancs.num_parcelamento = inscricao.num_parcelamento                 \n";

        $stSql .="              INNER JOIN
                                    (
                                        SELECT
                                            parcela_origem.valor AS valor_origem,
                                            parcela_origem.num_parcelamento,
                                            parcela.cod_lancamento

                                        FROM
                                            divida.parcela_origem

                                        INNER JOIN
                                            arrecadacao.parcela
                                        ON
                                            parcela.cod_parcela = parcela_origem.cod_parcela
                                    ) AS vorigparcela
                                ON
                                    vorigparcela.num_parcelamento = dp.num_parcelamento
                                    AND vorigparcela.cod_lancamento = lancs.cod_lancamento               \n";

        $stSql .="              INNER JOIN
                                    (
                                        SELECT
                                            sum(parcela_origem.valor) AS valor_origem,
                                            parcela_origem.num_parcelamento,
                                            parcela.cod_lancamento

                                        FROM
                                            divida.parcela_origem

                                        INNER JOIN
                                            arrecadacao.parcela
                                        ON
                                            parcela.cod_parcela = parcela_origem.cod_parcela

                                        GROUP BY
                                            parcela_origem.num_parcelamento, cod_lancamento
                                    ) AS vorig
                                ON
                                    vorig.num_parcelamento = dp.num_parcelamento
                                    AND vorig.cod_lancamento = lancs.cod_lancamento                     \n";

        $stSql .="          ".$stParametros."                                                           \n";
        $stSql .="      ) as lancamentos                                                                \n";

        $stSql .="  GROUP BY                                                                            \n";
        $stSql .="      cod_lancamento                                                                  \n";
        $stSql .="      , exercicio_original                                                            \n";
        $stSql .="      , origem, nom_origem, total_parcelas,valor_reducao, acrescimo, calculo,juros , multa , correcao                                            \n";

        return $stSql;

    }

    public function listaConsultaValoresOrigemDivida(&$rsRecordSet, $stParametros, $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stSql = $this->montaListaConsultaValoresOrigemDivida($stParametros);

        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaListaConsultaValoresOrigemDivida($stParametros)
    {
        $arDataBase = explode( "/",$this->getDado('data_base') );
        $stDataBase = $arDataBase[2]."-".$arDataBase[1]."-".$arDataBase[0];

        $stSql = "
            SELECT criarbuffertexto( 'boConsulta', 'true');
            SELECT
                '".$this->getDado('data_base')."' as data_base,
                tmp.valor_lancado,
                tmp.multa,
                tmp.juros,
                tmp.correcao,
                COALESCE( tmp.total_reducao, 0.00 ) AS total_reducao,
                tmp.exercicio_original,
                tmp.nom_origem,
                (tmp.valor_lancado + tmp.multa + tmp.juros + tmp.correcao) -
                (COALESCE( tmp.total_reducao, 0.00) * (tmp.valor_lancado + tmp.multa +tmp.juros + tmp.correcao)/COALESCE( tmp.acrescimo + tmp.calculo , 1 ))::numeric(12,2) AS valor_atualizado,
                tmp.total_parcelas
            FROM
                (
                    SELECT DISTINCT
                        (
                            SELECT
                                sum(dpo.valor)

                            FROM
                                divida.parcela_origem AS dpo

                            WHERE
                                                    dpo.num_parcelamento = ( SELECT divida.divida_parcelamento.num_parcelamento
                                                                                    FROM divida.divida_parcelamento
                                                                                   WHERE divida.divida_parcelamento.cod_inscricao = dda.cod_inscricao
                                                                                     AND divida.divida_parcelamento.exercicio = dda.exercicio
                                                                                ORDER BY divida.divida_parcelamento.num_parcelamento DESC
                                                                                   LIMIT 1
                                                                                )
                                                     AND dpo.cod_parcela IN ( SELECT dpo2.cod_parcela
                                                                                FROM divida.parcela_origem AS dpo2
                                                                               WHERE dpo2.num_parcelamento = (select min(num_parcelamento) from divida.divida_parcelamento where cod_inscricao = ddp.cod_inscricao and exercicio = ddp.exercicio)
                                                                                 AND dpo2.cod_parcela   = dpo.cod_parcela
                                                                                 AND dpo2.cod_especie  = dpo.cod_especie
                                                                                 AND dpo2.cod_genero   = dpo.cod_genero
                                                                                 AND dpo2.cod_natureza = dpo.cod_natureza
                                                                                 AND dpo2.cod_credito  = dpo.cod_credito
                                                                            )




                        )AS valor_lancado,
                        to_number (
                            aplica_acrescimo_modalidade(
                                0,
                                dda.cod_inscricao,
                                dda.exercicio::integer,
                                dpar.cod_modalidade,
                                3,
                                dpar.num_parcelamento,
                                COALESCE( (
                                    SELECT
                                        sum(dpo.valor)
                                    FROM
                                        divida.parcela_origem AS dpo
                                    WHERE
                                                   dpo.num_parcelamento = ( SELECT divida.divida_parcelamento.num_parcelamento
                                                                                    FROM divida.divida_parcelamento
                                                                                   WHERE divida.divida_parcelamento.cod_inscricao = dda.cod_inscricao
                                                                                     AND divida.divida_parcelamento.exercicio = dda.exercicio
                                                                                ORDER BY divida.divida_parcelamento.num_parcelamento DESC
                                                                                   LIMIT 1
                                                                                )
                                                     AND dpo.cod_parcela IN ( SELECT dpo2.cod_parcela
                                                                                FROM divida.parcela_origem AS dpo2
                                                                               WHERE dpo2.num_parcelamento = (select min(num_parcelamento) from divida.divida_parcelamento where cod_inscricao = ddp.cod_inscricao and exercicio = ddp.exercicio)
                                                                                 AND dpo2.cod_parcela   = dpo.cod_parcela
                                                                                 AND dpo2.cod_especie  = dpo.cod_especie
                                                                                 AND dpo2.cod_genero   = dpo.cod_genero
                                                                                 AND dpo2.cod_natureza = dpo.cod_natureza
                                                                                 AND dpo2.cod_credito  = dpo.cod_credito
                                                                            )
                                ), 0.00),
                                dda.dt_vencimento_origem,
                                '".$stDataBase."',
                                'false'
                            ),
                        '9999999999.99' ) AS multa,
                        to_number (
                            aplica_acrescimo_modalidade(
                                0,
                                dda.cod_inscricao,
                                dda.exercicio::integer,
                                dpar.cod_modalidade,
                                2,
                                dpar.num_parcelamento,
                                COALESCE( (
                                    SELECT
                                        sum(dpo.valor)
                                    FROM
                                        divida.parcela_origem AS dpo
                                    WHERE
                                                   dpo.num_parcelamento = ( SELECT divida.divida_parcelamento.num_parcelamento
                                                                                    FROM divida.divida_parcelamento
                                                                                   WHERE divida.divida_parcelamento.cod_inscricao = dda.cod_inscricao
                                                                                     AND divida.divida_parcelamento.exercicio = dda.exercicio
                                                                                ORDER BY divida.divida_parcelamento.num_parcelamento DESC
                                                                                   LIMIT 1
                                                                                )
                                                     AND dpo.cod_parcela IN ( SELECT dpo2.cod_parcela
                                                                                FROM divida.parcela_origem AS dpo2
                                                                               WHERE dpo2.num_parcelamento = (select min(num_parcelamento) from divida.divida_parcelamento where cod_inscricao = ddp.cod_inscricao and exercicio = ddp.exercicio)
                                                                                 AND dpo2.cod_parcela   = dpo.cod_parcela
                                                                                 AND dpo2.cod_especie  = dpo.cod_especie
                                                                                 AND dpo2.cod_genero   = dpo.cod_genero
                                                                                 AND dpo2.cod_natureza = dpo.cod_natureza
                                                                                 AND dpo2.cod_credito  = dpo.cod_credito
                                                                            )
                                ), 0.00 ),
                                dda.dt_vencimento_origem,
                                '".$stDataBase."',
                                'false'
                            ),
                        '9999999999.99' ) AS juros,
                        to_number (
                            aplica_acrescimo_modalidade(
                                0,
                                dda.cod_inscricao,
                                dda.exercicio::integer,
                                dpar.cod_modalidade,
                                1,
                                dpar.num_parcelamento,
                                COALESCE( (
                                    SELECT
                                        sum(dpo.valor)
                                    FROM
                                        divida.parcela_origem AS dpo
                                    WHERE
                                                   dpo.num_parcelamento = ( SELECT divida.divida_parcelamento.num_parcelamento
                                                                                    FROM divida.divida_parcelamento
                                                                                   WHERE divida.divida_parcelamento.cod_inscricao = dda.cod_inscricao
                                                                                     AND divida.divida_parcelamento.exercicio = dda.exercicio
                                                                                ORDER BY divida.divida_parcelamento.num_parcelamento DESC
                                                                                   LIMIT 1
                                                                                )
                                                     AND dpo.cod_parcela IN ( SELECT dpo2.cod_parcela
                                                                                FROM divida.parcela_origem AS dpo2
                                                                               WHERE dpo2.num_parcelamento = (select min(num_parcelamento) from divida.divida_parcelamento where cod_inscricao = ddp.cod_inscricao and exercicio = ddp.exercicio)
                                                                                 AND dpo2.cod_parcela   = dpo.cod_parcela
                                                                                 AND dpo2.cod_especie  = dpo.cod_especie
                                                                                 AND dpo2.cod_genero   = dpo.cod_genero
                                                                                 AND dpo2.cod_natureza = dpo.cod_natureza
                                                                                 AND dpo2.cod_credito  = dpo.cod_credito
                                                                            )
                                ), 0.00),
                                dda.dt_vencimento_origem,
                                '".$stDataBase."',
                                'false'
                            ),
                        '9999999999.99' ) AS correcao,
            (select sum(vlracrescimo) from divida.parcela_acrescimo where num_parcelamento = ddp.num_parcelamento) AS acrescimo,
                        (select sum(vl_credito) from divida.parcela_calculo where num_parcelamento = ddp.num_parcelamento) AS calculo,
                        (
                            SELECT
                                sum(valor)
                            FROM
                                divida.parcela_reducao
                            WHERE
                                divida.parcela_reducao.num_parcelamento = ddp.num_parcelamento
                        )AS total_reducao,
                        dda.exercicio_original,

                        arrecadacao.fn_busca_origem_inscricao_divida_ativa( dda.cod_inscricao, dda.exercicio::integer, 6 ) AS nom_origem,

                        (
                            SELECT
                                (
                                    SELECT
                                        arrecadacao.fn_total_parcelas_aberto( ap.cod_lancamento, dda.exercicio_original )
                                    FROM
                                        arrecadacao.parcela AS ap
                                    WHERE
                                        ap.cod_parcela = dpo.cod_parcela
                                )
                            FROM
                                divida.parcela_origem AS dpo
                            WHERE
                                dpo.num_parcelamento = (
                                    SELECT
                                        divida.divida_parcelamento.num_parcelamento
                                    FROM
                                        divida.divida_parcelamento
                                    WHERE
                                        divida.divida_parcelamento.cod_inscricao = dda.cod_inscricao
                                        AND divida.divida_parcelamento.exercicio = dda.exercicio
                                    ORDER BY
                                        divida.divida_parcelamento.num_parcelamento ASC
                                    LIMIT 1
                                )
                                AND dpo.cod_parcela IN (
                                    SELECT
                                        dpo2.cod_parcela
                                    FROM
                                        divida.parcela_origem AS dpo2
                                    WHERE
                                        dpo2.num_parcelamento = ddp.num_parcelamento
                                        AND dpo2.cod_parcela = dpo.cod_parcela
                                )
                                LIMIT 1
                        )as total_parcelas

                    FROM
                        divida.divida_ativa AS dda

                 INNER JOIN ( SELECT divida_parcelamento.cod_inscricao
                                   , divida_parcelamento.exercicio
                                   , max(divida_parcelamento.num_parcelamento) AS num_parcelamento
                                FROM divida.divida_parcelamento
               LEFT JOIN divida.parcelamento_cancelamento
                  ON divida_parcelamento.num_parcelamento = parcelamento_cancelamento.num_parcelamento
                   WHERE parcelamento_cancelamento.num_parcelamento IS NULL
                            GROUP BY divida_parcelamento.cod_inscricao
                                   , divida_parcelamento.exercicio
                            )AS ddp
                         ON ddp.cod_inscricao = dda.cod_inscricao
                        AND ddp.exercicio = dda.exercicio

                    INNER JOIN
                        divida.parcelamento AS dpar
                    ON
                        dpar.num_parcelamento = ddp.num_parcelamento

                    ".$stParametros."
                )AS tmp
        \n";

        return $stSql;
    }

    /**
    /* 19/07/2007 - Diego Bueno
    /* criada para listar as inscricoes na table tree de segundo nível da consulta da divida
    /* FMConsultaInscricaoDetalheCobrança
    /*
    /* TODO: alterar as configuracoes de DATABASE
    **/
    public function listaConsultaInscricoesSimples(&$rsRecordSet, $stParametros, $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stSql = $this->montaListaConsultaInscricoesSimples($stParametros);
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;

    }

    public function montaListaConsultaInscricoesSimples($stParametros)
    {
        $arDataBase = explode( "/",$this->getDado('data_base') );
        $stDataBase = $arDataBase[2]."-".$arDataBase[1]."-".$arDataBase[0];

        $stSql = "
SELECT
    *
, (
        SELECT DISTINCT
            count(ap.cod_parcela) as qtde
        FROM
            arrecadacao.parcela as ap
            INNER JOIN (
                SELECT
                    max(cod_credito) as cod_credito
                    , cod_parcela
                    , num_parcelamento
                FROM
                    divida.parcela_origem as dpo
                GROUP BY cod_parcela, num_parcelamento
            ) as dpo
            ON dpo.cod_parcela = ap.cod_parcela
        WHERE
            dpo.num_parcelamento = minimo.min_parcelamento
    ) as total_parcelas
    , arrecadacao.fn_busca_origem_inscricao_divida_ativa (minimo.cod_inscricao, minimo.exercicio::integer, 1 ) as origem
    , (
        SELECT
            sum(dpo.valor) AS origem
        FROM
            divida.parcela_origem AS dpo

        INNER JOIN
            divida.parcela_origem AS dpo_antigo
        ON
            dpo_antigo.cod_parcela = dpo.cod_parcela
            AND dpo_antigo.cod_especie = dpo.cod_especie
            AND dpo_antigo.cod_genero = dpo.cod_genero
            AND dpo_antigo.cod_natureza = dpo.cod_natureza
            AND dpo_antigo.cod_credito = dpo.cod_credito

        WHERE
            dpo.num_parcelamento = minimo.num_parcelamento
            AND dpo_antigo.num_parcelamento = minimo.min_parcelamento
    ) as valor_lancado

    , (
        SELECT
            sum(parcela_reducao.valor)
        FROM
            divida.parcela_reducao
        WHERE
            parcela_reducao.num_parcelamento = minimo.num_parcelamento
    )AS valor_reducao

    , (

        SELECT
             ( to_number ( aplica_acrescimo_modalidade( 0, minimo.cod_inscricao, minimo.exercicio, minimo.cod_modalidade, 1, minimo.num_parcelamento, origem, vencimento_origem, '".$stDataBase."', 'false' ), '9999999999.99' ) )
                +
             ( to_number ( aplica_acrescimo_modalidade( 0, minimo.cod_inscricao, minimo.exercicio, minimo.cod_modalidade, 2, minimo.num_parcelamento, origem, vencimento_origem, '".$stDataBase."', 'false' ), '9999999999.99' ) )
                +
             ( to_number ( aplica_acrescimo_modalidade( 0, minimo.cod_inscricao, minimo.exercicio, minimo.cod_modalidade, 3, minimo.num_parcelamento, origem, vencimento_origem, '".$stDataBase."', 'false' ), '9999999999.99' )
                +
               origem
             )

        FROM
            (

            SELECT
                sum(dpo.valor) AS origem
            FROM
                divida.parcela_origem AS dpo

            INNER JOIN
                divida.parcela_origem AS dpo_antigo
            ON
                dpo_antigo.cod_parcela = dpo.cod_parcela
                AND dpo_antigo.cod_especie = dpo.cod_especie
                AND dpo_antigo.cod_genero = dpo.cod_genero
                AND dpo_antigo.cod_natureza = dpo.cod_natureza
                AND dpo_antigo.cod_credito = dpo.cod_credito

            WHERE
                dpo.num_parcelamento = minimo.num_parcelamento
                AND dpo_antigo.num_parcelamento = minimo.min_parcelamento

        ) as valores_crus

    ) as valor_atualizado


FROM -- VALORES DA BUSCA


    (
        SELECT
            *
            ,(
                select
                    min(num_parcelamento) as num_parcelamento
                FROM
                    divida.divida_parcelamento as dp
                WHERE
                    dp.cod_inscricao = busca.cod_inscricao
                    AND dp.exercicio = busca.exercicio::varchar
            ) as min_parcelamento
        FROM
            (

                SELECT DISTINCT
                    inscricao.cod_inscricao
                    , inscricao.exercicio_original
                    , inscricao.num_parcelamento
                    , inscricao.exercicio
                    , inscricao.vencimento_origem
                    , dp.cod_modalidade
                    , inscricao.dt_inscricao
                FROM
                    (
                        SELECT
                            dda.cod_inscricao
                            , dda.exercicio::int
                            , ddp.num_parcelamento
                            --, max(ddp.num_parcelamento) as num_parcelamento
                            , dda.dt_vencimento_origem as vencimento_origem
                            , dda.exercicio_original::int
                            , dda.dt_inscricao
                        FROM
                            divida.divida_ativa AS dda

                            INNER JOIN divida.divida_parcelamento AS ddp
                            ON ddp.cod_inscricao = dda.cod_inscricao
                            AND ddp.exercicio = dda.exercicio

                        GROUP BY
                            dda.cod_inscricao, dda.exercicio, dda.dt_inscricao
                            , dda.dt_vencimento_origem , dda.exercicio_original,ddp.num_parcelamento
                    ) as inscricao

                    INNER JOIN divida.parcelamento AS dp
                    ON dp.num_parcelamento = inscricao.num_parcelamento

                ". $stParametros ."

                ORDER BY exercicio_original

            ) as busca

    ) as minimo


\n";

        return $stSql;

    }

/*
    public function ListaConsultaCobrancas(&$rsRecordSet, $stParametros, $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stSql = $this->montaListaConsultaCobrancas().$stParametros;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaListaConsultaCobrancas()
    {
     $stSql = "SELECT
                    CASE WHEN dp.numero_parcelamento = -1 THEN
                        NULL
                    ELSE
                        dp.numero_parcelamento||'/'||dp.exercicio
                    END AS numero_parcelamento,
    dp.numcgm_usuario,
    (
        SELECT
            nom_cgm
        FROM
            sw_cgm
        WHERE
            sw_cgm.numcgm = dp.numcgm_usuario
    )AS nomcgm_usuario,
    ddp.num_parcelamento,
    to_char(dp.timestamp, 'dd/mm/yyyy') AS dt_parcelamento,
    dp.cod_modalidade,
    (
        SELECT
            descricao
        FROM
            divida.modalidade
        WHERE
            divida.modalidade.cod_modalidade = dp.cod_modalidade
    )AS descricao_modalidade,
    (
        SELECT
            CASE WHEN ( count(*) > 0 ) THEN
                true
            ELSE
                false
            END
        FROM
            divida.parcela
        WHERE
            divida.parcela.num_parcelamento = ddp.num_parcelamento
    )AS ativar_lista,
        (
        SELECT
            count(*)
        FROM
            divida.parcela
        WHERE
            divida.parcela.num_parcelamento = ddp.num_parcelamento
            AND parcela.num_parcela > 0
    )AS qtd_parcelas,
    (
        SELECT
            count(num_parcela) as parcelas_pagas
        FROM
            divida.parcela
        WHERE
            dp.num_parcelamento = parcela.num_parcelamento
            AND parcela.paga = false
            AND parcela.cancelada = true
            AND parcela.num_parcela > 0
    ) as parcelas_canceladas,
    (
        SELECT
            count(num_parcela) as parcelas_pagas
        FROM
            divida.parcela
        WHERE
            dp.num_parcelamento = parcela.num_parcelamento
            AND parcela.paga = true
            AND parcela.cancelada = false
            AND parcela.num_parcela > 0
    ) as parcelas_pagas,
    mv.cod_tipo_modalidade,
    (
        SELECT
            sum(divida.parcela.vlr_parcela)
        FROM
            divida.parcela

        WHERE
            divida.parcela.num_parcelamento = ddp.num_parcelamento
            AND parcela.num_parcela > 0
    )AS valor_parcelamento
FROM
    divida.divida_parcelamento AS ddp
INNER JOIN
    divida.parcelamento AS dp
ON
    dp.num_parcelamento = ddp.num_parcelamento
INNER JOIN
    divida.modalidade_vigencia as mv
ON
    mv.cod_modalidade = dp.cod_modalidade
    AND mv.timestamp = dp.timestamp_modalidade
        ";

        return $stSql;
    }
*/

    public function ListaConsultaCobrancas(&$rsRecordSet, $stParametros, $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stSql = $this->montaListaConsultaCobrancas($stParametros);
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;

    }

    public function montaListaConsultaCobrancas($stParametros)
    {
        $stSql = "  SELECT                                                                              \n";
        $stSql .="      *                                                                               \n";
        $stSql .="      , ( CASE WHEN inscricao_estornada IS NOT NULL THEN
                                'Estornada'
                            WHEN inscricao_cancelada IS NOT NULL THEN
                                'Cancelada'
                            WHEN qtd_parcelas < 1 THEN                                                  \n";
        $stSql .="              'Em Aberto'                                                             \n";
        $stSql .="          WHEN parcelas_canceladas > 0 THEN                                           \n";
        $stSql .="              'Cancelada'                                                             \n";
        $stSql .="          WHEN ( parcelas_unicas_pagas > 0 ) THEN                                     \n";
        $stSql .="              'Quitada'                                                               \n";
        $stSql .="          WHEN qtd_parcelas > 0 AND ( qtd_parcelas = parcelas_pagas ) THEN            \n";
        $stSql .="              'Quitada'                                                               \n";
        $stSql .="          WHEN cod_tipo_modalidade = 2 THEN                                           \n";
        $stSql .="              'Consolidada'                                                           \n";
        $stSql .="          WHEN cod_tipo_modalidade = 3 THEN                                           \n";
        $stSql .="              'Parcelada'                                                             \n";
        $stSql .="          ELSE                                                                        \n";
        $stSql .="              'Aberta'                                                                \n";
        $stSql .="          END                                                                         \n";
        $stSql .="      ) as situacao                                                                   \n";
        $stSql .="      , '".$this->getDado('data_base')."' as data_base                                \n";

        $stSql .="  FROM                                                                                \n";

        $stSql .="      (                                                                               \n";
        $stSql .="          SELECT                                                                      \n";
        $stSql .="              ddp.num_parcelamento                                                    \n";
        $stSql .="              , ( CASE WHEN dp.numero_parcelamento = -1 THEN                          \n";
        $stSql .="                      NULL                                                            \n";
        $stSql .="                  ELSE                                                                \n";
        $stSql .="                      dp.numero_parcelamento||'/'||dp.exercicio                       \n";
        $stSql .="                  END                                                                 \n";
        $stSql .="              ) AS numero_parcelamento                                                \n";
        $stSql .="              , dp.cod_modalidade                                                     \n";
        $stSql .="              , mv.cod_tipo_modalidade                                                \n";
        $stSql .="              , dm.descricao as descricao_modalidade                                  \n";
        $stSql .="              , dp.numcgm_usuario                                                     \n";
        $stSql .="              , cgm.nom_cgm AS nomcgm_usuario                                         \n";
        $stSql .="              , to_char(dp.timestamp, 'dd/mm/yyyy') AS dt_parcelamento                \n";
        $stSql .="              , (                                                                     \n";
        $stSql .="                  SELECT                                                              \n";
        $stSql .="                      count(*)                                                        \n";
        $stSql .="                  FROM                                                                \n";
        $stSql .="                      divida.parcela as dp                                            \n";
        $stSql .="                  WHERE                                                               \n";
        $stSql .="                      dp.num_parcelamento = ddp.num_parcelamento                      \n";
        $stSql .="                      AND dp.num_parcela > 0                                          \n";
        $stSql .="              ) AS qtd_parcelas                                                       \n";
        $stSql .="              , (                                                                     \n";
        $stSql .="                  SELECT                                                              \n";
        $stSql .="                      count(*)                                                        \n";
        $stSql .="                  FROM                                                                \n";
        $stSql .="                      divida.parcela as dp                                            \n";
        $stSql .="                  WHERE                                                               \n";
        $stSql .="                      dp.num_parcelamento = ddp.num_parcelamento                      \n";
        $stSql .="                      AND dp.num_parcela = 0                                          \n";
        $stSql .="              ) AS qtd_unicas                                                         \n";
        $stSql .="              , (                                                                     \n";
        $stSql .="                  SELECT                                                              \n";
        $stSql .="                      count(num_parcela) as parcelas_pagas                            \n";
        $stSql .="                  FROM                                                                \n";
        $stSql .="                      divida.parcela                                                  \n";
        $stSql .="                  WHERE                                                               \n";
        $stSql .="                      dp.num_parcelamento = parcela.num_parcelamento                  \n";
        $stSql .="                      AND parcela.paga = false                                        \n";
        $stSql .="                      AND parcela.cancelada = true                                    \n";
        $stSql .="              ) as parcelas_canceladas                                                \n";
        $stSql .="              , (                                                                     \n";
        $stSql .="                  SELECT                                                              \n";
        $stSql .="                      count(num_parcela) as parcelas_pagas                            \n";
        $stSql .="                  FROM                                                                \n";
        $stSql .="                      divida.parcela                                                  \n";
        $stSql .="                  WHERE                                                               \n";
        $stSql .="                      dp.num_parcelamento = parcela.num_parcelamento                  \n";
        $stSql .="                      AND parcela.paga = true                                         \n";
        $stSql .="                      AND parcela.cancelada = false                                   \n";
        $stSql .="                      AND parcela.num_parcela > 0                                     \n";
        $stSql .="              ) as parcelas_pagas                                                     \n";
        $stSql .="              , (                                                                     \n";
        $stSql .="                  SELECT                                                              \n";
        $stSql .="                      count(num_parcela) as parcelas_pagas                            \n";
        $stSql .="                  FROM                                                                \n";
        $stSql .="                      divida.parcela                                                  \n";
        $stSql .="                  WHERE                                                               \n";
        $stSql .="                      dp.num_parcelamento = parcela.num_parcelamento                  \n";
        $stSql .="                      AND parcela.paga = true                                         \n";
        $stSql .="                      AND parcela.cancelada = false                                   \n";
        $stSql .="                      AND parcela.num_parcela = 0                                     \n";
        $stSql .="              ) as parcelas_unicas_pagas                                              \n";
        $stSql .="              , (                                                                     \n";
        $stSql .="                  SELECT                                                              \n";
        $stSql .="                      CASE WHEN ( count(*) > 0 ) THEN                                 \n";
        $stSql .="                          true                                                        \n";
        $stSql .="                      ELSE                                                            \n";
        $stSql .="                          false                                                       \n";
        $stSql .="                      END                                                             \n";
        $stSql .="                  FROM                                                                \n";
        $stSql .="                      divida.parcela                                                  \n";
        $stSql .="                  WHERE                                                               \n";
        $stSql .="                      num_parcelamento = ddp.num_parcelamento                         \n";
        $stSql .="              ) AS ativar_lista                                                       \n";
        $stSql .="              , (                                                                     \n";
        $stSql .="                  SELECT                                                              \n";
        $stSql .="                      sum(dpar.vlr_parcela)                                           \n";
        $stSql .="                  FROM                                                                \n";
        $stSql .="                      divida.parcela as dpar                                          \n";
        $stSql .="                  WHERE                                                               \n";
        $stSql .="                      dpar.num_parcelamento = ddp.num_parcelamento                    \n";
        $stSql .="                      AND dpar.num_parcela > 0                                        \n";
        $stSql .="              ) AS valor_parcelamento                                                 \n";
        $stSql .="              ,ddcanc.cod_inscricao AS inscricao_cancelada                            \n";
        $stSql .="              ,ddestorn.cod_inscricao AS inscricao_estornada                          \n";
        $stSql .="              , dpc.motivo as motivo_cancelamento                                     \n";
        $stSql .="              , to_char(dpc.timestamp, 'dd/mm/yyyy') as data_cancelamento                                    \n";
        $stSql .="              , dpc.numcgm ||' - '||(select nom_cgm from sw_cgm where numcgm = dpc.numcgm) as usuario_cancelamento \n";

        $stSql .="          FROM                                                                        \n";

        $stSql .="              divida.divida_parcelamento AS ddp                                       \n";

        $stSql .="              LEFT JOIN divida.divida_cancelada AS ddcanc
                                ON  ddcanc.cod_inscricao = ddp.cod_inscricao
                                AND ddcanc.exercicio = ddp.exercicio                                    \n";

        $stSql .="              LEFT JOIN divida.divida_estorno AS ddestorn
                                ON  ddestorn.cod_inscricao = ddp.cod_inscricao
                                AND ddestorn.exercicio = ddp.exercicio                                    \n";

        $stSql .="              INNER JOIN divida.parcelamento AS dp                                    \n";
        $stSql .="              ON dp.num_parcelamento = ddp.num_parcelamento                           \n";

        $stSql .="              LEFT JOIN divida.parcelamento_cancelamento as DPC                       \n";
        $stSql .="                ON DPC.num_parcelamento = ddp.num_parcelamento                        \n";

        $stSql .="              INNER JOIN divida.modalidade_vigencia as mv                             \n";
        $stSql .="              ON mv.cod_modalidade = dp.cod_modalidade                                \n";
        $stSql .="              AND mv.timestamp = dp.timestamp_modalidade                              \n";

        $stSql .="              INNER JOIN divida.modalidade as dm                                      \n";
        $stSql .="              ON dm.cod_modalidade = mv.cod_modalidade                                \n";
        $stSql .="              AND dp.timestamp_modalidade = mv.timestamp                              \n";

        $stSql .="              INNER JOIN sw_cgm as cgm                                                \n";
        $stSql .="              ON cgm.numcgm = dp.numcgm_usuario                                       \n";

        $stSql .="          ".$stParametros."                                                           \n";
        $stSql .="      ) as busca                                                                      \n";

        $stSql .="  WHERE                                                                               \n";
        $stSql .="      qtd_parcelas > 0                                                                \n";

        return $stSql;

    }

    /**  ######################### FIM NOVA FUNCAO */

    /**  LISTA CONSULTA PARCELAS */

    public function ListaConsultaParcelas(&$rsRecordSet, $stParametros, $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stSql = $this->montaListaConsultaParcelas().$stParametros;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

   public function montaListaConsultaParcelas()
   {
        $stSql = "
        SELECT distinct
            dp.num_parcelamento,
            to_char( coalesce(pag.data_pagamento, now()::date), 'dd/mm/YYYY')::varchar as database_br
                , ( dp.num_parcela ||'/'|| (
                        SELECT
                            count(*)
                        FROM
                            divida.parcela
                        WHERE
                            divida.parcela.num_parcelamento = dp.num_parcelamento
                            AND parcela.num_parcela > 0
                    )
                ) AS info_parcela
                , (
                    SELECT
                        count(*)
                    FROM
                        divida.parcela
                    WHERE
                        divida.parcela.num_parcelamento = dp.num_parcelamento
                        AND parcela.num_parcela > 0
                ) AS total_de_parcelas
                , dp.num_parcela
                , dp.vlr_parcela
                , to_char(dp.dt_vencimento_parcela, 'dd/mm/yyyy') AS vencimento
                , ( CASE WHEN dp.paga = true THEN
                        'paga'
                    ELSE
                        CASE WHEN dp.cancelada = true THEN
                            'cancelada'
                        ELSE
                            'aberta'
                        END
                    END
                ) AS situacao

                , alc.cod_lancamento
                , ap.cod_parcela
                , ac.numeracao
                , ac.exercicio
                , acm.numeracao_migracao
                , acm.prefixo

            FROM
                divida.parcela AS dp

                INNER JOIN divida.parcela_calculo AS dpc
                ON dpc.num_parcelamento = dp.num_parcelamento
                AND dpc.num_parcela = dp.num_parcela

                INNER JOIN arrecadacao.lancamento_calculo as alc
                ON alc.cod_calculo = dpc.cod_calculo

                INNER JOIN arrecadacao.calculo AS calc
                ON calc.cod_calculo = alc.cod_calculo

                INNER JOIN arrecadacao.parcela as ap
                ON ap.cod_lancamento = alc.cod_lancamento
                AND ap.nr_parcela = dp.num_parcela

                LEFT JOIN (
                    SELECT
                        MAX(app.timestamp) AS timestamp,
                        app.cod_parcela
                    FROM
                        arrecadacao.parcela_reemissao AS app
                    GROUP BY cod_parcela
                )as aparr
                ON aparr.cod_parcela = ap.cod_parcela

                INNER JOIN arrecadacao.carne as ac
                ON ac.cod_parcela = ap.cod_parcela
                AND ac.exercicio = calc.exercicio
                AND ( (aparr.timestamp = ac.timestamp) OR aparr IS NULL)

                LEFT JOIN arrecadacao.pagamento as pag
                ON pag.numeracao = ac.numeracao

                LEFT JOIN arrecadacao.carne_migracao AS acm
                ON acm.numeracao = ac.numeracao
                AND acm.cod_convenio = ac.cod_convenio
           WHERE
                dp.num_parcela >= 0
                    \n";

        return $stSql;
    }

    public function recuperaListaValorOriginalCredito(&$rsRecordSet, $stParametros, $stOrdem, $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stSql = $this->montaRecuperaListaValorOriginalCredito().$stParametros.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaListaValorOriginalCredito()
    {
        $stSql   = " SELECT
                        dpc.vl_credito,
                        ac.cod_credito,
                        ac.cod_especie,
                        ac.cod_natureza,
                        ac.cod_genero,
                        dp.num_parcelamento,
                        dp.num_parcela

                     FROM
                        divida.parcela AS dp

                     INNER JOIN
                        divida.parcela_calculo AS dpc
                     ON
                        dpc.num_parcelamento = dp.num_parcelamento
                        AND dpc.num_parcela = dp.num_parcela

                     INNER JOIN
                        arrecadacao.calculo AS ac
                     ON
                        ac.cod_calculo = dpc.cod_calculo
                    \n";

        return $stSql;
    }

    public function recuperaConsulta(&$rsRecordSet, $stFiltro = "", $dtDataBase, $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql  = $this->montaRecuperaConsulta( $dtDataBase, $stFiltro );
        $this->setDebug($stSql);
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao  );

        return $obErro;
    }

    public function montaRecuperaConsulta($dtDataBase, $stFiltro)
    {
        $stSql = "
            SELECT
                consulta.*
                , ( CASE WHEN consulta.pagamento_data is not null THEN
                        CASE WHEN
                            ( consulta.pagamento_valor !=
                            ( (consulta.parcela_valor - parcela_valor_desconto ) +
                            consulta.parcela_juros_pagar + consulta.parcela_multa_pagar
                            + parcela_correcao_pagar
                            + consulta.tmp_pagamento_diferenca )
                            )
                        THEN
                            coalesce (
                                consulta.pagamento_valor -
                                (( consulta.parcela_valor - consulta.parcela_valor_desconto ) +
                                ( consulta.parcela_juros_pagar )
                                + ( consulta.parcela_multa_pagar )
                                + ( consulta.parcela_correcao_pagar )
                                ), 0.00 )
                            + coalesce( (
                            ( consulta.parcela_juros_pago - consulta.parcela_juros_pagar )
                            + ( consulta.parcela_multa_pago - consulta.parcela_multa_pagar )
                            + ( consulta.parcela_correcao_pago - consulta.parcela_correcao_pagar )
                            ), 0.00 )
                        ELSE
                            consulta.tmp_pagamento_diferenca
                        END
                    ELSE
                        0.00
                    END
                ) as pagamento_diferenca
                , ( CASE WHEN  consulta.situacao = 'Em Aberto' THEN
                        consulta.parcela_juros_pagar
                    ELSE
                        CASE WHEN consulta.pagamento_data is not null THEN
                            consulta.parcela_juros_pago
                        ELSE
                            0.00
                        END
                    END
                ) as parcela_juros
                , ( CASE WHEN  consulta.situacao = 'Em Aberto' THEN
                        consulta.parcela_multa_pagar
                    ELSE
                        CASE WHEN consulta.pagamento_data is not null THEN
                            consulta.parcela_multa_pago
                        ELSE
                            0.00
                        END
                    END
                ) as parcela_multa
                , ( CASE WHEN consulta.situacao = 'Em Aberto' THEN
                        ( consulta.parcela_valor - parcela_valor_desconto
                        + consulta.parcela_juros_pagar + consulta.parcela_multa_pagar
                        + consulta.parcela_correcao_pagar )
                    ELSE
                        CASE WHEN consulta.pagamento_data is not null THEN
                            consulta.pagamento_valor
                        ELSE
                            0.00
                        END
                    END
                ) as valor_total
            FROM
                (
                    select DISTINCT
                        al.cod_lancamento
                        , carne.numeracao
                        , carne.exercicio
                        , carne.cod_convenio
                    ---- PARCELA
                        , ap.cod_parcela
                        , ap.nr_parcela
                        , ( CASE WHEN apr.cod_parcela is not null THEN
                                to_char (arrecadacao.fn_atualiza_data_vencimento(apr.vencimento),
                                'dd/mm/YYYY')
                            ELSE
                                to_char (arrecadacao.fn_atualiza_data_vencimento(ap.vencimento),
                                'dd/mm/YYYY')
                            END
                        )::varchar as parcela_vencimento_original
                        , ( CASE WHEN apr.cod_parcela is null THEN
                                arrecadacao.fn_atualiza_data_vencimento(ap.vencimento)
                            ELSE
                                arrecadacao.fn_atualiza_data_vencimento(apr.vencimento)
                            END
                        )::varchar as parcela_vencimento_US
                        , ap.valor as parcela_valor
                        , ( CASE WHEN apd.cod_parcela is not null THEN
                                (ap.valor - apd.valor)
                            ELSE
                                0.00
                            END
                        )::numeric(14,2) as parcela_valor_desconto
                        , ( select arrecadacao.buscaValorOriginalParcela( carne.numeracao ) as valor
                        ) as parcela_valor_original
                        , ( CASE WHEN apd.cod_parcela is not null THEN
                                arrecadacao.fn_percentual_desconto_parcela( ap.cod_parcela,
                                ap.vencimento, (carne.exercicio)::int )
                            ELSE
                                0.00
                            END
                        ) as parcela_desconto_percentual
                        , ( CASE WHEN ap.nr_parcela = 0 THEN
                                'Única'::VARCHAR
                            ELSE
                                ap.nr_parcela::varchar||'/'||
                                arrecadacao.fn_total_parcelas(al.cod_lancamento)
                            END
                        ) as info_parcela
                        , ( CASE WHEN apag.numeracao is not null THEN
                                apag.pagamento_tipo
                            ELSE
                                CASE WHEN acd.devolucao_data is not null THEN
                                    acd.devolucao_descricao
                                ELSE
                                    CASE WHEN dp.paga = true THEN
                                        'Paga'
                                    ELSE
                                        CASE WHEN ap.nr_parcela = 0
                                                    and (ap.vencimento < '".$dtDataBase."')
                                        THEN
                                            'Cancelada (Parcela única vencida)'
                                        ELSE
                                            'Em Aberto'
                                        END
                                    END
                                END
                            END
                        )::varchar as situacao
                    ---- PARCELA FIM
                        , al.valor as lancamento_valor
                    ---- PAGAMENTO
                        , to_char(apag.pagamento_data,'dd/mm/YYYY') as pagamento_data
                        , apag.pagamento_data_baixa
                        , apag.processo_pagamento
                        , apag.observacao
                        , apag.tp_pagamento
                        , apag.pagamento_tipo
                        , pag_lote.pagamento_cod_lote
                        , coalesce ( apag_dif.pagamento_diferenca, 0.00 ) as tmp_pagamento_diferenca
                        , apag.pagamento_valor
                        , ( CASE WHEN pag_lote.numeracao is not null THEN
                                pag_lote.cod_banco
                            ELSE
                                pag_lote_manual.cod_banco
                            END
                        ) as pagamento_cod_banco
                        , ( CASE WHEN pag_lote.numeracao is not null THEN
                                pag_lote.num_banco
                            ELSE
                                pag_lote_manual.num_banco
                            END
                        ) as pagamento_num_banco
                        , ( CASE WHEN pag_lote.numeracao is not null THEN
                                pag_lote.nom_banco
                            ELSE
                                pag_lote_manual.nom_banco
                            END
                        ) as pagamento_nom_banco
                        , ( CASE WHEN pag_lote.numeracao is not null THEN
                                pag_lote.cod_agencia
                            ELSE
                                pag_lote_manual.cod_agencia
                            END
                        ) as pagamento_cod_agencia
                        , ( CASE WHEN pag_lote.numeracao is not null THEN
                                pag_lote.num_agencia
                            ELSE
                                pag_lote_manual.num_agencia
                            END
                        ) as pagamento_num_agencia
                        , ( CASE WHEN pag_lote.numeracao is not null THEN
                                pag_lote.nom_agencia
                            ELSE
                                pag_lote_manual.nom_agencia
                            END
                        ) as pagamento_nom_agencia
                        , ( CASE WHEN pag_lote.numeracao is not null THEN
                                pag_lote.numcgm
                            ELSE
                                apag.pagamento_cgm
                            END
                        ) as pagamento_numcgm
                        , ( CASE WHEN pag_lote.numeracao is not null THEN
                                pag_lote.nom_cgm
                            ELSE
                                apag.pagamento_nome
                            END
                        ) as pagamento_nomcgm
                        , apag.ocorrencia_pagamento
                    ---- CARNE DEVOLUCAO
                        , acd.devolucao_data
                        , acd.devolucao_descricao
                    ---- CARNE MIGRACAO
                        , acm.numeracao_migracao as migracao_numeracao
                        , acm.prefixo as migracao_prefixo
                    ---- CONSOLIDACAO
                        , accon.numeracao_consolidacao as consolidacao_numeracao
                    ---- PARCELA ACRESCIMOS
                        , ( CASE WHEN  ( ap.vencimento >= '".$dtDataBase."' AND ap.nr_parcela > 0 )
                                        OR ( ap.valor = 0.00 )
                                        OR ( apag.pagamento_data is not null
                                            AND ap.vencimento >= apag.pagamento_data )
                                        OR ( ap.nr_parcela > 0 AND acd.numeracao is not null )
                            THEN
                                0.00
                            ELSE
                                --arrecadacao.calcula_correcao_lancamento(carne.numeracao,'".$dtDataBase."')::numeric(14,2)
                                split_part( aplica_acrescimo_modalidade( 0, ddc.cod_inscricao, ddc.exercicio::integer, dip.cod_modalidade, 1, dp.num_parcelamento, ap.valor, ap.vencimento, '".$dtDataBase."', 'true' ), ';', 1 )::numeric(14,2)
                            END
                        )::numeric(14,2) as parcela_correcao_pagar
                        , ( CASE WHEN  ( ap.vencimento >= '".$dtDataBase."' AND ap.nr_parcela > 0 )
                                        OR ( ap.valor = 0.00 )
                                        OR ( apag.pagamento_data is not null
                                            AND ap.vencimento >= apag.pagamento_data )
                                        OR ( ap.nr_parcela > 0 AND acd.numeracao is not null )
                            THEN
                                0.00
                            ELSE
                                --arrecadacao.calcula_juros_lancamento(carne.numeracao,'".$dtDataBase."')::numeric(14,2)
                                split_part( aplica_acrescimo_modalidade( 0, ddc.cod_inscricao, ddc.exercicio::integer, dip.cod_modalidade, 2, dp.num_parcelamento, ap.valor, ap.vencimento, '".$dtDataBase."', 'true' ), ';', 1 )::numeric(14,2)
                            END
                        )::numeric(14,2) as parcela_juros_pagar
                        , ( CASE WHEN  ( ap.vencimento >= '".$dtDataBase."' AND ap.nr_parcela > 0 )
                                        OR ( ap.valor = 0.00 )
                                        OR (apag.pagamento_data is not null
                                            AND ap.vencimento >= apag.pagamento_data
                                        )
                                        OR ( ap.nr_parcela > 0 AND acd.numeracao is not null )
                            THEN
                                0.00
                            ELSE
                                --arrecadacao.calcula_multa_lancamento ( carne.numeracao, '".$dtDataBase."')::numeric
                                split_part( aplica_acrescimo_modalidade( 0, ddc.cod_inscricao, ddc.exercicio::integer, dip.cod_modalidade, 3, dp.num_parcelamento, ap.valor, ap.vencimento, '".$dtDataBase."', 'true' ), ';', 1 )::numeric(14,2)
                            END
                        )::numeric(14,2) as parcela_multa_pagar
                        , ( CASE WHEN ( apag.pagamento_data is not null
                                        AND ap.vencimento < apag.pagamento_data )
                            THEN
                                ( select
                                        sum(valor)
                                    from
                                        arrecadacao.pagamento_acrescimo
                                    where
                                        numeracao = apag.numeracao
                                        AND cod_convenio = apag.cod_convenio
                                        AND ocorrencia_pagamento = apag.ocorrencia_pagamento
                                        AND cod_tipo = 1
                                )
                            ELSE
                                0.00
                            END
                        )::numeric(14,2) as parcela_correcao_pago
                        , ( CASE WHEN ( apag.pagamento_data is not null
                                        AND ap.vencimento < apag.pagamento_data )
                            THEN
                                ( select
                                        sum(valor)
                                    from
                                        arrecadacao.pagamento_acrescimo
                                    where
                                        numeracao = apag.numeracao
                                        AND cod_convenio = apag.cod_convenio
                                        AND ocorrencia_pagamento = apag.ocorrencia_pagamento
                                        AND cod_tipo = 3
                                )
                            ELSE
                                0.00
                            END
                        )::numeric(14,2) as parcela_multa_pago
                        , ( CASE WHEN ( apag.pagamento_data is not null AND
                                        ap.vencimento < apag.pagamento_data )
                            THEN
                                ( select
                                    sum(valor)
                                    from
                                    arrecadacao.pagamento_acrescimo
                                    where
                                    numeracao = apag.numeracao
                                    AND cod_convenio = apag.cod_convenio
                                    AND ocorrencia_pagamento = apag.ocorrencia_pagamento
                                    AND cod_tipo = 2
                                )
                            ELSE
                                0.00
                            END
                        )::numeric(14,2) as parcela_juros_pago
            FROM
                arrecadacao.carne as carne
            ---- PARCELA
                INNER JOIN (
                    select
                        cod_parcela
                        , valor
                        , arrecadacao.fn_atualiza_data_vencimento (vencimento) as vencimento
                        , nr_parcela
                        , cod_lancamento
                    from
                        arrecadacao.parcela as ap
                ) as ap
                ON ap.cod_parcela = carne.cod_parcela

                LEFT JOIN (
                    select
                        apr.cod_parcela
                        , arrecadacao.fn_atualiza_data_vencimento( vencimento ) as vencimento
                        , valor
                    from
                        arrecadacao.parcela_reemissao apr
                        inner join (
                            select cod_parcela, min(timestamp) as timestamp
                            from arrecadacao.parcela_reemissao
                            group by cod_parcela
                        ) as apr2
                        ON apr2.cod_parcela = apr.cod_parcela
                        AND apr2.timestamp = apr.timestamp
                ) as apr
                ON apr.cod_parcela = ap.cod_parcela

                LEFT JOIN arrecadacao.parcela_desconto apd
                ON apd.cod_parcela = ap.cod_parcela
                ---- #
                INNER JOIN arrecadacao.lancamento as al
                ON al.cod_lancamento = ap.cod_lancamento
                INNER JOIN arrecadacao.lancamento_calculo as alc
                ON alc.cod_lancamento = al.cod_lancamento

                INNER JOIN divida.parcela_calculo AS dpc
                ON  dpc.cod_calculo = alc.cod_calculo
                AND dpc.num_parcela = ap.nr_parcela

                INNER JOIN
                        divida.parcela AS dp
                ON
                        dp.num_parcelamento = dpc.num_parcelamento
                        AND dp.num_parcela = dpc.num_parcela

                INNER JOIN
                        divida.parcelamento AS dip
                ON
                        dip.num_parcelamento = dpc.num_parcelamento

                INNER JOIN
                        divida.divida_parcelamento AS ddp
                ON
                        ddp.num_parcelamento = dpc.num_parcelamento

                INNER JOIN
                        divida.divida_cgm AS ddc
                ON
                        ddc.cod_inscricao = ddp.cod_inscricao
                        AND ddc.exercicio = ddp.exercicio

                INNER JOIN arrecadacao.calculo as ac
                ON ac.cod_calculo = alc.cod_calculo
            ---- PAGAMENTO
                LEFT JOIN (
                    SELECT
                        apag.numeracao
                        , apag.cod_convenio
                        , apag.observacao
                        , atp.pagamento as tp_pagamento
                        , apag.data_pagamento as pagamento_data
                        , to_char(apag.data_baixa,'dd/mm/YYYY') as pagamento_data_baixa
                        , app.cod_processo::varchar||'/'||app.ano_exercicio as processo_pagamento
                        , cgm.numcgm as pagamento_cgm
                        , cgm.nom_cgm as pagamento_nome
                        , atp.nom_tipo as pagamento_tipo
                        , apag.valor as pagamento_valor
                        , apag.ocorrencia_pagamento
                    FROM
                        arrecadacao.pagamento as apag
                        INNER JOIN sw_cgm as cgm
                        ON cgm.numcgm = apag.numcgm
                        INNER JOIN arrecadacao.tipo_pagamento as atp
                        ON atp.cod_tipo = apag.cod_tipo
                        LEFT JOIN arrecadacao.processo_pagamento as app
                        ON app.numeracao = apag.numeracao AND app.cod_convenio = apag.cod_convenio
                ) as apag
                ON apag.numeracao = carne.numeracao
                AND apag.cod_convenio = carne.cod_convenio
                LEFT JOIN (
                    SELECT
                        numeracao
                        , cod_convenio
                        , ocorrencia_pagamento
                        , sum( valor ) as pagamento_diferenca
                    FROM arrecadacao.pagamento_diferenca
                    GROUP BY numeracao, cod_convenio, ocorrencia_pagamento
                ) as apag_dif
                ON apag_dif.numeracao = carne.numeracao
                AND apag_dif.cod_convenio = carne.cod_convenio
                AND apag_dif.ocorrencia_pagamento = apag.ocorrencia_pagamento
            ---- PAGAMENTO LOTE AUTOMATICO
                LEFT JOIN (
                    SELECT
                        pag_lote.numeracao
                        , pag_lote.cod_convenio
                        , lote.cod_lote as pagamento_cod_lote
                        , cgm.numcgm
                        , cgm.nom_cgm
                        , lote.data_lote
                        , mb.cod_banco
                        , mb.num_banco
                        , mb.nom_banco
                        , mag.cod_agencia
                        , mag.num_agencia
                        , mag.nom_agencia
                        , pag_lote.ocorrencia_pagamento
                    FROM
                        arrecadacao.pagamento_lote pag_lote
                        INNER JOIN arrecadacao.lote lote
                        ON lote.cod_lote = pag_lote.cod_lote
                        AND pag_lote.exercicio = lote.exercicio
                        INNER JOIN monetario.banco as mb ON mb.cod_banco = lote.cod_banco
                        INNER JOIN sw_cgm cgm ON cgm.numcgm = lote.numcgm
                        LEFT JOIN monetario.conta_corrente_convenio mccc
                        ON mccc.cod_convenio = pag_lote.cod_convenio
                        LEFT JOIN monetario.agencia mag
                        ON mag.cod_agencia = lote.cod_agencia
                        AND mag.cod_banco = mb.cod_banco
                ) as pag_lote
                ON pag_lote.numeracao = carne.numeracao
                AND pag_lote.cod_convenio = carne.cod_convenio
            ----- PAGAMENTO LOTE MANUAL
                LEFT JOIN (
                    SELECT
                        pag_lote.numeracao
                        , pag_lote.cod_convenio
                        , mb.cod_banco
                        , mb.num_banco
                        , mb.nom_banco
                        , mag.cod_agencia
                        , mag.num_agencia
                        , mag.nom_agencia
                        , pag_lote.ocorrencia_pagamento
                    FROM
                        arrecadacao.pagamento_lote_manual pag_lote
                        INNER JOIN monetario.banco as mb ON mb.cod_banco = pag_lote.cod_banco
                        LEFT JOIN monetario.conta_corrente_convenio mccc
                        ON mccc.cod_convenio = pag_lote.cod_convenio
                        LEFT JOIN monetario.agencia mag
                        ON mag.cod_agencia = pag_lote.cod_agencia
                        AND mag.cod_banco = mb.cod_banco
                ) as pag_lote_manual
                ON pag_lote_manual.numeracao = carne.numeracao
                AND pag_lote_manual.cod_convenio = carne.cod_convenio
                AND pag_lote_manual.ocorrencia_pagamento = apag.ocorrencia_pagamento
            ---- CARNE DEVOLUCAO
                LEFT JOIN (
                    SELECT
                        acd.numeracao
                        , acd.cod_convenio
                        , acd.dt_devolucao as devolucao_data
                        , amd.descricao as devolucao_descricao
                    FROM
                        arrecadacao.carne_devolucao as acd
                        INNER JOIN arrecadacao.motivo_devolucao as amd
                        ON amd.cod_motivo = acd.cod_motivo
                ) as acd
                ON acd.numeracao = carne.numeracao
                AND acd.cod_convenio = carne.cod_convenio
                LEFT JOIN arrecadacao.carne_migracao acm
                ON  acm.numeracao  = carne.numeracao
                AND acm.cod_convenio = carne.cod_convenio
                LEFT JOIN arrecadacao.carne_consolidacao as accon
                ON accon.numeracao = carne.numeracao
                AND accon.cod_convenio = carne.cod_convenio
            WHERE
                ". $stFiltro ."
            ORDER BY
                ap.nr_parcela
            ) as consulta ";

        return $stSql;
    }

    public function recuperaListaCarnesParaCancelar(&$rsRecordSet, $inCodParcela, $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stSql = $this->montaRecuperaListaCarnesParaCancelar($inCodParcela);
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaListaCarnesParaCancelar($inCodParcela)
    {
        $stSql   = "    SELECT
                            numeracao,
                            cod_convenio
                        FROM
                            arrecadacao.carne

                        INNER JOIN
                            arrecadacao.parcela
                        ON
                            parcela.cod_parcela = carne.cod_parcela

                        WHERE
                            parcela.cod_lancamento in (
                                SELECT DISTINCT
                                    cod_lancamento
                                FROM
                                    arrecadacao.parcela
                                WHERE
                                    parcela.cod_parcela = ".$inCodParcela."
                            )AND carne.numeracao NOT IN (
                                SELECT
                                    pagamento.numeracao
                                FROM
                                    arrecadacao.pagamento
                                WHERE
                                    pagamento.numeracao = carne.numeracao
                                    AND pagamento.cod_convenio = carne.cod_convenio
                            )AND carne.numeracao NOT IN (
                                SELECT
                                    carne_devolucao.numeracao
                                FROM
                                    arrecadacao.carne_devolucao
                                WHERE
                                    carne_devolucao.numeracao = carne.numeracao
                                    AND carne_devolucao.cod_convenio = carne.cod_convenio
                                    AND carne_devolucao.cod_motivo <> 10
                            )

                    \n";

        return $stSql;
    }

    public function recuperaConsultaTermoInscricaoDivida(&$rsRecordSet, $stFiltro, $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stSql = $this->montaRecuperaConsultaTermoInscricaoDivida().$stFiltro;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaConsultaTermoInscricaoDivida()
    {
        $stSql   = " SELECT DISTINCT ( SELECT procurador.oab
                                         FROM divida.autoridade
                                   INNER JOIN divida.procurador
                                           ON procurador.cod_autoridade = autoridade.cod_autoridade
                                        WHERE autoridade.cod_autoridade = dda.cod_autoridade
                                     )AS oab
                          , ( SELECT ( SELECT nom_cgm
                                         FROM sw_cgm
                                        WHERE sw_cgm.numcgm = autoridade.numcgm
                                     )
                                FROM divida.autoridade
                          INNER JOIN divida.procurador
                                  ON procurador.cod_autoridade = autoridade.cod_autoridade
                               WHERE autoridade.cod_autoridade = dda.cod_autoridade
                            )AS procurador
                          , to_char(now(), 'dd/mm/yyyy') AS dt_notificacao
                          , ( SELECT COALESCE( sw_cgm_pessoa_fisica.cpf, sw_cgm_pessoa_juridica.cnpj )
                                FROM sw_cgm
                           LEFT JOIN sw_cgm_pessoa_fisica
                                  ON sw_cgm_pessoa_fisica.numcgm = sw_cgm.numcgm
                           LEFT JOIN sw_cgm_pessoa_juridica
                                  ON sw_cgm_pessoa_juridica.numcgm = sw_cgm.numcgm
                               WHERE sw_cgm.numcgm = ddc.numcgm
                            )AS cpf_cnpj
                          , CASE WHEN ddi.inscricao_municipal IS NOT NULL THEN
                               'im'
                            ELSE
                               CASE WHEN dde.inscricao_economica IS NOT NULL THEN
                                  'ie'
                               ELSE
                                  'cgm'
                               END
                            END AS tipo_inscricao
                          , ddi.inscricao_municipal
                          , dde.inscricao_economica
                          , dda.cod_inscricao
                          , dda.exercicio
                          , dda.num_livro
                          , dda.num_folha
                          , COALESCE( ddi.inscricao_municipal, dde.inscricao_economica, ddc.numcgm) AS inscricao
                          , CASE WHEN ddi.inscricao_municipal IS NOT NULL THEN
                               arrecadacao.fn_consulta_endereco_todos( ddi.inscricao_municipal, 1, 1 )
                            ELSE
                               CASE WHEN dde.inscricao_economica IS NOT NULL THEN
                                  arrecadacao.fn_consulta_endereco_todos( dde.inscricao_economica, 2, 1 )
                               ELSE
                                  arrecadacao.fn_consulta_endereco_todos( ddc.numcgm, 3, 1 )
                               END
                            END AS endereco
                          , CASE WHEN ddi.inscricao_municipal IS NOT NULL THEN
                               arrecadacao.fn_consulta_endereco_todos( ddi.inscricao_municipal, 1, 2 )
                            ELSE
                               CASE WHEN dde.inscricao_economica IS NOT NULL THEN
                                  arrecadacao.fn_consulta_endereco_todos( dde.inscricao_economica, 2, 2 )
                               ELSE
                                  arrecadacao.fn_consulta_endereco_todos( ddc.numcgm, 3, 2 )
                               END
                            END AS bairro
                          , CASE WHEN ddi.inscricao_municipal IS NOT NULL THEN
                               arrecadacao.fn_consulta_endereco_todos( ddi.inscricao_municipal, 1, 3 )
                            ELSE
                               CASE WHEN dde.inscricao_economica IS NOT NULL THEN
                                  arrecadacao.fn_consulta_endereco_todos( dde.inscricao_economica, 2, 3 )
                               ELSE
                                  arrecadacao.fn_consulta_endereco_todos( ddc.numcgm, 3, 3 )
                               END
                            END AS cep
                          , ( SELECT sum(dpo.valor)
                                FROM divida.parcela_origem AS dpo
                               WHERE dpo.num_parcelamento = ( SELECT divida.divida_parcelamento.num_parcelamento
                                                                FROM divida.divida_parcelamento
                                                               WHERE divida.divida_parcelamento.cod_inscricao = dda.cod_inscricao
                                                                 AND divida.divida_parcelamento.exercicio = dda.exercicio
                                                            ORDER BY divida.divida_parcelamento.num_parcelamento DESC
                                                               LIMIT 1
                                                            )
                                 AND dpo.cod_parcela IN ( SELECT dpo2.cod_parcela
                                                            FROM divida.parcela_origem AS dpo2
                                                           WHERE dpo2.num_parcelamento = (select min(num_parcelamento) from divida.divida_parcelamento where cod_inscricao = ddp.cod_inscricao and exercicio = ddp.exercicio)
                                                            AND dpo2.cod_parcela   = dpo.cod_parcela
                                                            AND dpo2.cod_especie  = dpo.cod_especie
                                                            AND dpo2.cod_genero   = dpo.cod_genero
                                                            AND dpo2.cod_natureza = dpo.cod_natureza
                                                            AND dpo2.cod_credito  = dpo.cod_credito
                                                        )
                            )AS valor_origem
                          , (SELECT CASE WHEN tabela.split_part = '' THEN '0.00' ELSE tabela.split_part END AS multa FROM (SELECT split_part (
                                aplica_acrescimo_modalidade( 0,
                                    dda.cod_inscricao,
                                    dda.exercicio::integer,
                                    dpar.cod_modalidade,
                                    3,
                                    dpar.num_parcelamento,
                                    COALESCE( ( SELECT sum(dpo.valor)
                                                    FROM divida.parcela_origem AS dpo
                                                   WHERE dpo.num_parcelamento = ( SELECT divida.divida_parcelamento.num_parcelamento
                                                                                    FROM divida.divida_parcelamento
                                                                                   WHERE divida.divida_parcelamento.cod_inscricao = dda.cod_inscricao
                                                                                     AND divida.divida_parcelamento.exercicio = dda.exercicio
                                                                                ORDER BY divida.divida_parcelamento.num_parcelamento DESC
                                                                                   LIMIT 1
                                                                                )
                                                     AND dpo.cod_parcela IN ( SELECT dpo2.cod_parcela
                                                                                FROM divida.parcela_origem AS dpo2
                                                                               WHERE dpo2.num_parcelamento = (select min(num_parcelamento) from divida.divida_parcelamento where cod_inscricao = ddp.cod_inscricao and exercicio = ddp.exercicio)
                                                                                 AND dpo2.cod_parcela   = dpo.cod_parcela
                                                                                 AND dpo2.cod_especie  = dpo.cod_especie
                                                                                 AND dpo2.cod_genero   = dpo.cod_genero
                                                                                 AND dpo2.cod_natureza = dpo.cod_natureza
                                                                                 AND dpo2.cod_credito  = dpo.cod_credito
                                                                            )
                                              ), 0.00
                                    ),
                                    dda.dt_vencimento_origem,
                                    CASE WHEN (parcela.dt_vencimento_parcela IS NULL) THEN
                                            dda.dt_inscricao
                                         ELSE
                                            parcela.dt_vencimento_parcela
                                         END ,
                                    'false'
                                ),
                              ';',
                              2
                            )) AS tabela ) AS multa

                          , (SELECT CASE WHEN tabela.split_part = '' THEN '0.00' ELSE tabela.split_part END AS multa FROM (SELECT split_part (
                                aplica_acrescimo_modalidade( 0,
                                    dda.cod_inscricao,
                                    dda.exercicio::integer,
                                    dpar.cod_modalidade,
                                    3,
                                    dpar.num_parcelamento,
                                    COALESCE( ( SELECT sum(dpo.valor)
                                                FROM divida.parcela_origem AS dpo
                                               WHERE dpo.num_parcelamento = ( SELECT divida.divida_parcelamento.num_parcelamento
                                                                                FROM divida.divida_parcelamento
                                                                               WHERE divida.divida_parcelamento.cod_inscricao = dda.cod_inscricao
                                                                                 AND divida.divida_parcelamento.exercicio = dda.exercicio
                                                                            ORDER BY divida.divida_parcelamento.num_parcelamento DESC
                                                                               LIMIT 1
                                                                            )
                                                 AND dpo.cod_parcela IN ( SELECT dpo2.cod_parcela
                                                                            FROM divida.parcela_origem AS dpo2
                                                                           WHERE dpo2.num_parcelamento = (select min(num_parcelamento) from divida.divida_parcelamento where cod_inscricao = ddp.cod_inscricao and exercicio = ddp.exercicio)
                                                                                 AND dpo2.cod_parcela   = dpo.cod_parcela
                                                                                 AND dpo2.cod_especie  = dpo.cod_especie
                                                                                 AND dpo2.cod_genero   = dpo.cod_genero
                                                                                 AND dpo2.cod_natureza = dpo.cod_natureza
                                                                                 AND dpo2.cod_credito  = dpo.cod_credito
                                                                        )
                                               ),
                                             0.00
                                    ),
                                    dda.dt_vencimento_origem,
                                    CASE WHEN (parcela.dt_vencimento_parcela IS NULL) THEN
                                            dda.dt_inscricao
                                         ELSE
                                            parcela.dt_vencimento_parcela
                                         END ,
                                    'false'
                                ),
                              ';',
                              5
                            )) AS tabela ) AS multa_infracao

                          , split_part(
                                aplica_acrescimo_modalidade( 0,
                                    dda.cod_inscricao,
                                    dda.exercicio::integer,
                                    dpar.cod_modalidade,
                                    2,
                                    dpar.num_parcelamento,
                                    COALESCE( ( SELECT sum(dpo.valor)
                                                FROM divida.parcela_origem AS dpo
                                               WHERE dpo.num_parcelamento = ( SELECT divida.divida_parcelamento.num_parcelamento
                                                                                FROM divida.divida_parcelamento
                                                                               WHERE divida.divida_parcelamento.cod_inscricao = dda.cod_inscricao
                                                                                 AND divida.divida_parcelamento.exercicio = dda.exercicio
                                                                            ORDER BY divida.divida_parcelamento.num_parcelamento DESC
                                                                               LIMIT 1
                                                                            )
                                                 AND dpo.cod_parcela IN ( SELECT dpo2.cod_parcela
                                                                            FROM divida.parcela_origem AS dpo2
                                                                           WHERE dpo2.num_parcelamento = (select min(num_parcelamento) from divida.divida_parcelamento where cod_inscricao = ddp.cod_inscricao and exercicio = ddp.exercicio)
                                                                                 AND dpo2.cod_parcela   = dpo.cod_parcela
                                                                                 AND dpo2.cod_especie  = dpo.cod_especie
                                                                                 AND dpo2.cod_genero   = dpo.cod_genero
                                                                                 AND dpo2.cod_natureza = dpo.cod_natureza
                                                                                 AND dpo2.cod_credito  = dpo.cod_credito
                                                                        )
                                                ),
                                             0.00
                                    ),
                                    dda.dt_vencimento_origem,
                                    CASE WHEN (parcela.dt_vencimento_parcela IS NULL) THEN
                                            dda.dt_inscricao
                                         ELSE
                                            parcela.dt_vencimento_parcela
                                         END ,
                                    'false'
                                ),
                                ';',
                                1
                            ) AS juros
                          , split_part(
                                aplica_acrescimo_modalidade( 0,
                                    dda.cod_inscricao,
                                    dda.exercicio::integer,
                                    dpar.cod_modalidade,
                                    1,
                                    dpar.num_parcelamento,
                                    COALESCE( ( SELECT sum(dpo.valor)
                                                FROM divida.parcela_origem AS dpo
                                               WHERE dpo.num_parcelamento = ( SELECT divida.divida_parcelamento.num_parcelamento
                                                                                FROM divida.divida_parcelamento
                                                                               WHERE divida.divida_parcelamento.cod_inscricao = dda.cod_inscricao
                                                                                 AND divida.divida_parcelamento.exercicio = dda.exercicio
                                                                            ORDER BY divida.divida_parcelamento.num_parcelamento DESC
                                                                               LIMIT 1
                                                                            )
                                                 AND dpo.cod_parcela IN ( SELECT dpo2.cod_parcela
                                                                            FROM divida.parcela_origem AS dpo2
                                                                           WHERE dpo2.num_parcelamento = (select min(num_parcelamento) from divida.divida_parcelamento where cod_inscricao = ddp.cod_inscricao and exercicio = ddp.exercicio)
                                                                                 AND dpo2.cod_parcela   = dpo.cod_parcela
                                                                                 AND dpo2.cod_especie  = dpo.cod_especie
                                                                                 AND dpo2.cod_genero   = dpo.cod_genero
                                                                                 AND dpo2.cod_natureza = dpo.cod_natureza
                                                                                 AND dpo2.cod_credito  = dpo.cod_credito
                                                                        )
                                              ),
                                            0.00
                                    ),
                                    dda.dt_vencimento_origem,
                                    CASE WHEN (parcela.dt_vencimento_parcela IS NULL) THEN
                                            dda.dt_inscricao
                                         ELSE
                                            parcela.dt_vencimento_parcela
                                         END ,
                                    'false'
                                ),
                                ';',
                                1
                            ) AS correcao

                          , ( SELECT sum(valor)
                                FROM divida.parcela_reducao
                               WHERE divida.parcela_reducao.num_parcelamento = ddp.num_parcelamento
                            )AS total_reducao

                          , dda.exercicio_original AS exercicio_origem
                          , arrecadacao.fn_busca_origem_inscricao_divida_ativa( dda.cod_inscricao, dda.exercicio::integer, 4 ) AS imposto
                          , ( SELECT nom_cgm
                                FROM sw_cgm
                               WHERE sw_cgm.numcgm = ddc.numcgm
                            )AS contribuinte
                          , CASE WHEN ddi.inscricao_municipal IS NOT NULL THEN
                            ( SELECT COALESCE( ( SELECT valor
                                                   FROM imobiliario.atributo_lote_urbano_valor as ialu
                                                  WHERE ialu.cod_atributo = 5
                                                    AND ialu.cod_lote = il.cod_lote
                                               ORDER BY ialu.timestamp DESC
                                                  limit 1
                                               )
                                             , ( SELECT valor
                                                   FROM imobiliario.atributo_lote_rural_valor as ialr
                                                  WHERE ialr.cod_atributo = 5
                                                    AND ialr.cod_lote = il.cod_lote
                                               ORDER BY ialr.timestamp DESC
                                                  limit 1
                                               )
                                             )
                                FROM imobiliario.lote as il
                               WHERE il.cod_lote = imobiliario.fn_busca_lote_imovel( COALESCE( ( SELECT domicilio_fiscal.inscricao_municipal
                                                                                                   FROM economico.domicilio_fiscal
                                                                                                  WHERE domicilio_fiscal.inscricao_economica = dde.inscricao_economica
                                                                                               ),
                                                                                               ddi.inscricao_municipal
                                                                                     )
                                                                                   )
                            ) END AS numero_quadra
                            , CASE WHEN ddi.inscricao_municipal IS NOT NULL THEN
                                ( SELECT COALESCE( ( SELECT valor
                                                   FROM imobiliario.atributo_lote_urbano_valor as ialu
                                                  WHERE ialu.cod_atributo = 7
                                                    AND ialu.cod_lote = il.cod_lote
                                               ORDER BY ialu.timestamp DESC
                                                  limit 1
                                               ), ( SELECT valor
                                                      FROM imobiliario.atributo_lote_rural_valor as ialr
                                                     WHERE ialr.cod_atributo = 7
                                                       AND ialr.cod_lote = il.cod_lote
                                                  ORDER BY ialr.timestamp DESC
                                                     limit 1
                                               )
                                    )
                                FROM imobiliario.lote as il
                               WHERE il.cod_lote = imobiliario.fn_busca_lote_imovel( COALESCE( ( SELECT domicilio_fiscal.inscricao_municipal
                                                                                                   FROM economico.domicilio_fiscal
                                                                                                  WHERE domicilio_fiscal.inscricao_economica = dde.inscricao_economica
                                                                                               ),
                                                                                               ddi.inscricao_municipal
                                                                                     )
                                                                                   )
                            ) END AS numero_lote
                          , CASE WHEN ddi.inscricao_municipal IS NOT NULL THEN
                                ( select split_part ( imobiliario.fn_busca_endereco_imovel( ddi.inscricao_municipal ), '§', 1)||' '||split_part ( imobiliario.fn_busca_endereco_imovel( ddi.inscricao_municipal ), '§', 3)||', '||split_part ( imobiliario.fn_busca_endereco_imovel( ddi.inscricao_municipal ), '§', 4) )
                            ELSE
                               CASE WHEN dde.inscricao_economica IS NOT NULL THEN
                                   ( select split_part ( COALESCE( economico.fn_busca_domicilio_fiscal( dde.inscricao_economica ), economico.fn_busca_domicilio_informado( dde.inscricao_economica ) ), '§', 1 ))||' '||(select split_part ( COALESCE( economico.fn_busca_domicilio_fiscal( dde.inscricao_economica ), economico.fn_busca_domicilio_informado( dde.inscricao_economica ) ), '§', 3 ))||', '||(select split_part ( COALESCE( economico.fn_busca_domicilio_fiscal( dde.inscricao_economica ), economico.fn_busca_domicilio_informado( dde.inscricao_economica ) ), '§', 4 ))
                               ELSE
                                   ( SELECT sw_cgm.logradouro ||' '|| sw_cgm.numero ||' '|| sw_cgm.complemento
                                       FROM sw_cgm
                                      WHERE sw_cgm.numcgm = ddc.numcgm
                                   )
                               END
                            END AS domicilio_fiscal
                          , dpar.num_parcelamento
                          , to_char( dda.dt_inscricao, 'dd/mm/yyyy' ) AS dt_inscricao_divida
                       FROM divida.divida_ativa AS dda
                 INNER JOIN divida.divida_cgm AS ddc
                         ON ddc.cod_inscricao = dda.cod_inscricao
                        AND ddc.exercicio = dda.exercicio
                  LEFT JOIN divida.divida_imovel AS ddi
                         ON ddi.cod_inscricao = ddc.cod_inscricao
                        AND ddi.exercicio = ddc.exercicio
                  LEFT JOIN divida.divida_empresa AS dde
                         ON dde.cod_inscricao = ddc.cod_inscricao
                        AND dde.exercicio = ddc.exercicio
                 INNER JOIN ( SELECT divida_parcelamento.cod_inscricao
                                   , divida_parcelamento.exercicio
                                   , max(divida_parcelamento.num_parcelamento) AS num_parcelamento
                                FROM divida.divida_parcelamento
               LEFT JOIN divida.parcelamento_cancelamento
                  ON divida_parcelamento.num_parcelamento = parcelamento_cancelamento.num_parcelamento
                   WHERE parcelamento_cancelamento.num_parcelamento IS NULL
                            GROUP BY divida_parcelamento.cod_inscricao
                                   , divida_parcelamento.exercicio
                            )AS ddp
                         ON ddp.cod_inscricao = ddc.cod_inscricao
                        AND ddp.exercicio = ddc.exercicio
                 INNER JOIN divida.parcelamento AS dpar
                         ON dpar.num_parcelamento = ddp.num_parcelamento
                 LEFT JOIN divida.parcela
                         ON parcela.num_parcelamento = dpar.num_parcelamento
                        AND parcela.num_parcela      = 1
                   \n";

        return $stSql;
    }

    //esta funcao eh igual a recuperaConsultaNotificacaoDivida porem detalha os dados em funcao dos creditos
    public function recuperaConsultaNotificacaoDividaMata(&$rsRecordSet, $stFiltro, $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stSql = $this->montaRecuperaConsultaNotificacaoDividaMata().$stFiltro;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaConsultaNotificacaoDividaMata()
    {
        $stSql = "
             SELECT
             --  DISTINCT
             ( SELECT il.nom_localizacao
                              FROM imobiliario.lote_localizacao AS ill
                        INNER JOIN imobiliario.localizacao AS il
                                ON il.cod_localizacao = ill.cod_localizacao
                        INNER JOIN imobiliario.localizacao_nivel AS iln
                                ON il.codigo_composto = iln.valor || '.00'
                               AND iln.cod_localizacao = ill.cod_localizacao
                               AND iln.cod_nivel = 1
                             WHERE ill.cod_lote = imobiliario.fn_busca_lote_imovel( COALESCE( ( SELECT domicilio_fiscal.inscricao_municipal
                                                                                                  FROM economico.domicilio_fiscal
                                                                                                 WHERE domicilio_fiscal.inscricao_economica = dde.inscricao_economica
                                                                                               )
                                                                                             , ddi.inscricao_municipal
                                                                                             )
                                                                                   )
                          )AS regiao
                        , ( SELECT il.nom_localizacao
                              FROM imobiliario.lote_localizacao AS ill
                        INNER JOIN imobiliario.localizacao AS il
                                ON il.cod_localizacao = ill.cod_localizacao
                             WHERE ill.cod_lote = imobiliario.fn_busca_lote_imovel( COALESCE( ( SELECT domicilio_fiscal.inscricao_municipal
                                                                                                  FROM economico.domicilio_fiscal
                                                                                                 WHERE domicilio_fiscal.inscricao_economica = dde.inscricao_economica
                                                                                              )
                                                                                             , ddi.inscricao_municipal
                                                                                            )
                                                                                  )
                          )AS distrito
                        , CASE WHEN ddi.inscricao_municipal IS NOT NULL THEN
                            ( select split_part ( imobiliario.fn_busca_endereco_imovel( ddi.inscricao_municipal ), '§', 2) )
                          ELSE
                              CASE WHEN dde.inscricao_economica IS NOT NULL THEN
                                split_part( COALESCE( economico.fn_busca_domicilio_fiscal( dde.inscricao_economica ), economico.fn_busca_domicilio_informado( dde.inscricao_economica ) ), '§', 2 )
                              ELSE
                                0::text
                              END
                          END AS cod_logradouro
                        , CASE WHEN ddi.inscricao_municipal IS NOT NULL THEN
                            ( select split_part ( imobiliario.fn_busca_endereco_imovel( ddi.inscricao_municipal ), '§', 9)||'/'||split_part ( imobiliario.fn_busca_endereco_imovel( ddi.inscricao_municipal ), '§', 11) )
                          END AS cidade_estado
                        , sw_cgm.bairro AS bairro_notificado
                        , sw_cgm.cep AS cep_notificado
                        , ( SELECT sw_municipio.nom_municipio || '/' || sw_uf.nom_uf
                              FROM sw_cgm
                        INNER JOIN sw_uf
                                ON sw_uf.cod_pais = sw_cgm.cod_pais
                               AND sw_uf.cod_uf = sw_cgm.cod_uf
                        INNER JOIN sw_municipio
                                ON sw_municipio.cod_municipio = sw_cgm.cod_municipio
                               AND sw_municipio.cod_uf = sw_cgm.cod_uf
                             WHERE sw_cgm.numcgm = ddc.numcgm
                          )AS cidade_estado_notificado
                        , sw_cgm.logradouro ||' '|| sw_cgm.numero ||' '|| sw_cgm.complemento AS endereco_notificado
                        , COALESCE
                          (
                            (SELECT cpf  FROM sw_cgm_pessoa_fisica   WHERE sw_cgm_pessoa_fisica.numcgm   = sw_cgm.numcgm),
                            (SELECT cnpj FROM sw_cgm_pessoa_juridica WHERE sw_cgm_pessoa_juridica.numcgm = sw_cgm.numcgm)
                          ) AS cpf_cnpj_notificado
                        , ( SELECT COALESCE( ( SELECT valor
                                                 FROM imobiliario.atributo_lote_urbano_valor as ialu
                                                WHERE ialu.cod_atributo = 5
                                                  AND ialu.cod_lote = il.cod_lote
                                             ORDER BY ialu.timestamp DESC
                                                limit 1
                                             )
                                           , ( SELECT valor
                                                 FROM imobiliario.atributo_lote_rural_valor as ialr
                                                WHERE ialr.cod_atributo = 5
                                                  AND ialr.cod_lote = il.cod_lote
                                             ORDER BY ialr.timestamp DESC
                                                limit 1
                                             )
                                           )

                              FROM imobiliario.lote as il
                             WHERE il.cod_lote = imobiliario.fn_busca_lote_imovel( COALESCE( ( SELECT domicilio_fiscal.inscricao_municipal
                                                                                                 FROM economico.domicilio_fiscal
                                                                                                WHERE domicilio_fiscal.inscricao_economica = dde.inscricao_economica
                                                                                             )
                                                                                            , ddi.inscricao_municipal
                                                                                           )
                                                                                 )
                          ) AS numero_quadra
                        , ( SELECT COALESCE( ( SELECT valor
                                                 FROM imobiliario.atributo_lote_urbano_valor as ialu
                                                WHERE ialu.cod_atributo = 7
                                                  AND ialu.cod_lote = il.cod_lote
                                             ORDER BY ialu.timestamp DESC
                                                limit 1
                                             )
                                           , ( SELECT valor
                                                 FROM imobiliario.atributo_lote_rural_valor as ialr
                                                WHERE ialr.cod_atributo = 7
                                                  AND ialr.cod_lote = il.cod_lote
                                             ORDER BY ialr.timestamp DESC
                                                limit 1
                                             )
                                           )
                              FROM imobiliario.lote as il
                             WHERE il.cod_lote = imobiliario.fn_busca_lote_imovel( COALESCE( ( SELECT domicilio_fiscal.inscricao_municipal
                                                                                                 FROM economico.domicilio_fiscal
                                                                                                WHERE domicilio_fiscal.inscricao_economica = dde.inscricao_economica
                                                                                             )
                                                                                           , ddi.inscricao_municipal
                                                                                           )
                                                                                 )
                          ) AS numero_lote
                        , to_char(dda.dt_vencimento_origem, 'dd/mm/yyyy') AS dt_vencimento_origem
                        , CASE WHEN ddi.inscricao_municipal IS NOT NULL THEN
                            'im'
                          ELSE
                            CASE WHEN dde.inscricao_economica IS NOT NULL THEN
                              'ie'
                            ELSE
                              'cgm'
                            END
                          END AS tipo_inscricao
                        , ddi.inscricao_municipal
                        , dde.inscricao_economica
                        , dda.num_livro
                        , dda.num_folha
                        , COALESCE( ddi.inscricao_municipal, dde.inscricao_economica, ddc.numcgm) AS inscricao

                        , ( SELECT sum(dpo.valor)
                                FROM divida.parcela_origem AS dpo
                               WHERE dpo.num_parcelamento = ( SELECT divida.divida_parcelamento.num_parcelamento
                                                                FROM divida.divida_parcelamento
                                                               WHERE divida.divida_parcelamento.cod_inscricao = dda.cod_inscricao
                                                                 AND divida.divida_parcelamento.exercicio = dda.exercicio
                                                            ORDER BY divida.divida_parcelamento.num_parcelamento ASC
                                                               LIMIT 1
                                                            )
                                 AND dpo.cod_parcela IN ( SELECT dpo2.cod_parcela
                                                            FROM divida.parcela_origem AS dpo2
                                                           WHERE dpo2.num_parcelamento = ddp.num_parcelamento
                                                             AND dpo2.cod_parcela = dpo.cod_parcela
                                                        )
                            )AS valor_origem
             -- , origem.valor AS valor_origem

                        , split_part( aplica_acrescimo_modalidade( CASE WHEN dpar.judicial = FALSE THEN 0 ELSE 1 END
                                                                 , dda.cod_inscricao
                                                                 , dda.exercicio::integer
                                                                 , dpar.cod_modalidade
                                                                 , 1
                                                                 , dpar.num_parcelamento
                                                                 , COALESCE( ( SELECT sum(dpo.valor)
                                                                            FROM divida.parcela_origem AS dpo
                                                                           WHERE dpo.num_parcelamento = ( SELECT divida.divida_parcelamento.num_parcelamento
                                                                                                            FROM divida.divida_parcelamento
                                                                                                           WHERE divida.divida_parcelamento.cod_inscricao = dda.cod_inscricao
                                                                                                             AND divida.divida_parcelamento.exercicio = dda.exercicio
                                                                                                        ORDER BY divida.divida_parcelamento.num_parcelamento ASC
                                                                                                           LIMIT 1
                                                                                                        )
                                                                             AND dpo.cod_parcela IN ( SELECT dpo2.cod_parcela
                                                                                                        FROM divida.parcela_origem AS dpo2
                                                                                                       WHERE dpo2.num_parcelamento = ddp.num_parcelamento
                                                                                                         AND dpo2.cod_parcela = dpo.cod_parcela
                                                                                                    )
                                                                        ),
                                                                      0.00
                                                                 )
                                                                 , dda.dt_vencimento_origem
                                                                 , COALESCE(dparc.dt_vencimento_parcela,NOW()::DATE)
                                                                 , 'false' )
                                    , ';'
                                    , 1 ) AS correcao

                        , split_part( aplica_acrescimo_modalidade( CASE WHEN dpar.judicial = FALSE THEN 0 ELSE 1 END
                                                                 , dda.cod_inscricao
                                                                 , dda.exercicio::integer
                                                                 , dpar.cod_modalidade
                                                                 , 2
                                                                 , dpar.num_parcelamento
                                                                 , COALESCE( ( SELECT sum(dpo.valor)
                                                                                FROM divida.parcela_origem AS dpo
                                                                               WHERE dpo.num_parcelamento = ( SELECT divida.divida_parcelamento.num_parcelamento
                                                                                                                FROM divida.divida_parcelamento
                                                                                                               WHERE divida.divida_parcelamento.cod_inscricao = dda.cod_inscricao
                                                                                                                 AND divida.divida_parcelamento.exercicio = dda.exercicio
                                                                                                            ORDER BY divida.divida_parcelamento.num_parcelamento ASC
                                                                                                               LIMIT 1
                                                                                                            )
                                                                                 AND dpo.cod_parcela IN ( SELECT dpo2.cod_parcela
                                                                                                            FROM divida.parcela_origem AS dpo2
                                                                                                           WHERE dpo2.num_parcelamento = ddp.num_parcelamento
                                                                                                             AND dpo2.cod_parcela = dpo.cod_parcela
                                                                                                        )
                                                                              ),
                                                                           0.00
                                                                  )
                                                                 , dda.dt_vencimento_origem
                                                                 , COALESCE(dparc.dt_vencimento_parcela,NOW()::DATE)
                                                                 , 'false' )
                                    , ';'
                                    , 1 ) AS juros


                        ,COALESCE(NULLIF(split_part( aplica_acrescimo_modalidade( CASE WHEN dpar.judicial = FALSE THEN 0 ELSE 1 END
                                                                 , dda.cod_inscricao
                                                                 , dda.exercicio::integer
                                                                 , dpar.cod_modalidade
                                                                 , 3
                                                                 , dpar.num_parcelamento
                                                                 , COALESCE( ( SELECT sum(dpo.valor)
                                                                                FROM divida.parcela_origem AS dpo
                                                                               WHERE dpo.num_parcelamento = ( SELECT divida.divida_parcelamento.num_parcelamento
                                                                                                                FROM divida.divida_parcelamento
                                                                                                               WHERE divida.divida_parcelamento.cod_inscricao = dda.cod_inscricao
                                                                                                                 AND divida.divida_parcelamento.exercicio = dda.exercicio
                                                                                                            ORDER BY divida.divida_parcelamento.num_parcelamento ASC
                                                                                                               LIMIT 1
                                                                                                            )
                                                                                 AND dpo.cod_parcela IN ( SELECT dpo2.cod_parcela
                                                                                                            FROM divida.parcela_origem AS dpo2
                                                                                                           WHERE dpo2.num_parcelamento = ddp.num_parcelamento
                                                                                                             AND dpo2.cod_parcela = dpo.cod_parcela
                                                                                                        )
                                                                             ),
                                                                           0.00
                                                                  )
                                                                 , dda.dt_vencimento_origem
                                                                 , COALESCE(dparc.dt_vencimento_parcela,NOW()::DATE)
                                                                 , 'false' )
                                    , ';'
                                    , 5),''),'0.00')::numeric AS multa_infracao


                        ,COALESCE(NULLIF(split_part( aplica_acrescimo_modalidade( CASE WHEN dpar.judicial = FALSE THEN 0 ELSE 1 END
                                                                 , dda.cod_inscricao
                                                                 , dda.exercicio::integer
                                                                 , dpar.cod_modalidade
                                                                 , 3
                                                                 , dpar.num_parcelamento
                                                                 , COALESCE( ( SELECT sum(dpo.valor)
                                                                                FROM divida.parcela_origem AS dpo
                                                                               WHERE dpo.num_parcelamento = ( SELECT divida.divida_parcelamento.num_parcelamento
                                                                                                                FROM divida.divida_parcelamento
                                                                                                               WHERE divida.divida_parcelamento.cod_inscricao = dda.cod_inscricao
                                                                                                                 AND divida.divida_parcelamento.exercicio = dda.exercicio
                                                                                                            ORDER BY divida.divida_parcelamento.num_parcelamento ASC
                                                                                                               LIMIT 1
                                                                                                            )
                                                                                 AND dpo.cod_parcela IN ( SELECT dpo2.cod_parcela
                                                                                                            FROM divida.parcela_origem AS dpo2
                                                                                                           WHERE dpo2.num_parcelamento = ddp.num_parcelamento
                                                                                                             AND dpo2.cod_parcela = dpo.cod_parcela
                                                                                                        )
                                                                            ), 0.00
                                                                )
                                                                 , dda.dt_vencimento_origem
                                                                 , COALESCE(dparc.dt_vencimento_parcela,NOW()::DATE)
                                                                 , 'false' )
                                    , ';'
                                    , 2 ),''),'0.00')::numeric AS multa

                                    ,aplica_reducao_modalidade_acrescimo(
                                                                  dpar.cod_modalidade
                                                                 , dpar.num_parcelamento
                                                                 , COALESCE(NULLIF(split_part( aplica_acrescimo_modalidade( CASE WHEN dpar.judicial = FALSE THEN 0 ELSE 1 END
                                                                 , dda.cod_inscricao
                                                                 , dda.exercicio::integer
                                                                 , dpar.cod_modalidade
                                                                 , 2
                                                                 , dpar.num_parcelamento
                                                                 , COALESCE( ( SELECT sum(dpo.valor)
                                                                                FROM divida.parcela_origem AS dpo
                                                                               WHERE dpo.num_parcelamento = ( SELECT divida.divida_parcelamento.num_parcelamento
                                                                                                                FROM divida.divida_parcelamento
                                                                                                               WHERE divida.divida_parcelamento.cod_inscricao = dda.cod_inscricao
                                                                                                                 AND divida.divida_parcelamento.exercicio = dda.exercicio
                                                                                                            ORDER BY divida.divida_parcelamento.num_parcelamento ASC
                                                                                                               LIMIT 1
                                                                                                            )
                                                                                 AND dpo.cod_parcela IN ( SELECT dpo2.cod_parcela
                                                                                                            FROM divida.parcela_origem AS dpo2
                                                                                                           WHERE dpo2.num_parcelamento = ddp.num_parcelamento
                                                                                                             AND dpo2.cod_parcela = dpo.cod_parcela
                                                                                                        )
                                                                              ),
                                                                           0.00
                                                                  )
                                                                 , dda.dt_vencimento_origem
                                                                 , COALESCE(dparc.dt_vencimento_parcela,NOW()::DATE)
                                                                 , 'false' )
                                    , ';'
                                    , 1 ),''),'0.00')::numeric
                                                                , 2
                                                                , 2
                                                                , dda.dt_vencimento_origem
                                                                , 1
                                                                 )  AS reducao_juros

                                        ,aplica_reducao_modalidade_acrescimo(
                                                                  dpar.cod_modalidade
                                                                 , dpar.num_parcelamento
                                                                 , COALESCE(NULLIF(split_part( aplica_acrescimo_modalidade( CASE WHEN dpar.judicial = FALSE THEN 0 ELSE 1 END
                                                                 , dda.cod_inscricao
                                                                 , dda.exercicio::integer
                                                                 , dpar.cod_modalidade
                                                                 , 3
                                                                 , dpar.num_parcelamento
                                                                 , COALESCE( ( SELECT sum(dpo.valor)
                                                                                FROM divida.parcela_origem AS dpo
                                                                               WHERE dpo.num_parcelamento = ( SELECT divida.divida_parcelamento.num_parcelamento
                                                                                                                FROM divida.divida_parcelamento
                                                                                                               WHERE divida.divida_parcelamento.cod_inscricao = dda.cod_inscricao
                                                                                                                 AND divida.divida_parcelamento.exercicio = dda.exercicio
                                                                                                            ORDER BY divida.divida_parcelamento.num_parcelamento ASC
                                                                                                               LIMIT 1
                                                                                                            )
                                                                                 AND dpo.cod_parcela IN ( SELECT dpo2.cod_parcela
                                                                                                            FROM divida.parcela_origem AS dpo2
                                                                                                           WHERE dpo2.num_parcelamento = ddp.num_parcelamento
                                                                                                             AND dpo2.cod_parcela = dpo.cod_parcela
                                                                                                        )
                                                                             ),
                                                                           0.00
                                                                  )
                                                                 , dda.dt_vencimento_origem
                                                                 , COALESCE(dparc.dt_vencimento_parcela,NOW()::DATE)
                                                                 , 'false' )
                                    , ';'
                                    ,2),''),'0.00')::numeric
                                                                , 3
                                                                , 3
                                                                , dda.dt_vencimento_origem
                                                                , 1
                                                                 ) AS reducao_multa




                        , dda.exercicio_original AS exercicio_origem
                        , sw_cgm.nom_cgm AS nome_notificado
                        , CASE WHEN ddi.inscricao_municipal IS NOT NULL THEN
                            ( select split_part ( imobiliario.fn_busca_endereco_imovel( ddi.inscricao_municipal ), '§', 1)||' '||split_part ( imobiliario.fn_busca_endereco_imovel( ddi.inscricao_municipal ), '§', 3)||', '||split_part ( imobiliario.fn_busca_endereco_imovel( ddi.inscricao_municipal ), '§', 4) )
                          ELSE
                            CASE WHEN dde.inscricao_economica IS NOT NULL THEN
                              ( select split_part ( COALESCE( economico.fn_busca_domicilio_fiscal( dde.inscricao_economica ), economico.fn_busca_domicilio_informado( dde.inscricao_economica ) ), '§', 1 ))||' '||(select split_part ( COALESCE( economico.fn_busca_domicilio_fiscal( dde.inscricao_economica ), economico.fn_busca_domicilio_informado( dde.inscricao_economica ) ), '§', 3 ))||', '||(select split_part ( COALESCE( economico.fn_busca_domicilio_fiscal( dde.inscricao_economica ), economico.fn_busca_domicilio_informado( dde.inscricao_economica ) ), '§', 4 ))
                            ELSE
                              sw_cgm.logradouro ||' '|| sw_cgm.numero ||' '|| sw_cgm.complemento
                            END
                          END AS domicilio_fiscal
                        , dpar.num_parcelamento
                        , origem.descricao_credito

                     FROM divida.divida_ativa AS dda

               INNER JOIN divida.divida_cgm AS ddc
                       ON ddc.cod_inscricao = dda.cod_inscricao
                      AND ddc.exercicio = dda.exercicio

               INNER JOIN sw_cgm
                       ON sw_cgm.numcgm = ddc.numcgm

                LEFT JOIN divida.divida_imovel AS ddi
                       ON ddi.cod_inscricao = ddc.cod_inscricao
                      AND ddi.exercicio = ddc.exercicio

                LEFT JOIN divida.divida_empresa AS dde
                       ON dde.cod_inscricao = ddc.cod_inscricao
                      AND dde.exercicio = ddc.exercicio

               INNER JOIN ( SELECT divida_parcelamento.cod_inscricao
                                 , divida_parcelamento.exercicio
                                 , max(divida_parcelamento.num_parcelamento) AS num_parcelamento
                              FROM divida.divida_parcelamento
                              WHERE NOT EXISTS ( SELECT 1
                                                  FROM divida.parcelamento_cancelamento
                                                 WHERE parcelamento_cancelamento.num_parcelamento = divida_parcelamento.num_parcelamento )
                          GROUP BY divida_parcelamento.cod_inscricao
                                 , divida_parcelamento.exercicio
                          )AS ddp
                       ON ddp.cod_inscricao = dda.cod_inscricao
                      AND ddp.exercicio = dda.exercicio

               INNER JOIN divida.parcelamento AS dpar
                       ON dpar.num_parcelamento = ddp.num_parcelamento

                LEFT JOIN divida.parcela AS dparc
                       ON dpar.num_parcelamento = dparc.num_parcelamento
                      AND dparc.num_parcela = 1


               INNER JOIN (
                                   SELECT parcela_origem.valor
                    --   , parcela_origem.cod_parcela
                         , parcela_origem.num_parcelamento
                         , credito.descricao_credito
                         , divida_parcelamento.exercicio
                         , divida_parcelamento.cod_inscricao
                      FROM divida.parcela_origem
                INNER JOIN divida.divida_parcelamento
                        ON divida_parcelamento.num_parcelamento = parcela_origem.num_parcelamento
                INNER JOIN (   SELECT MIN(num_parcelamento) AS num_parcelamento
                                    , cod_inscricao
                                    , exercicio
                                 FROM divida.divida_parcelamento
                             GROUP BY cod_inscricao
                                      , exercicio
                           ) AS minimo
                        ON minimo.cod_inscricao = divida_parcelamento.cod_inscricao
                       AND minimo.exercicio     = divida_parcelamento.exercicio
                INNER JOIN (   SELECT MIN(cod_parcela) AS cod_parcela
                                    , cod_credito
                                    , cod_especie
                                    , cod_genero
                                    , cod_natureza
                                    , num_parcelamento
                                FROM divida.parcela_origem AS p1";

    if ($this->getDado('num_parcelamento') != '') {
       $stSql.=" WHERE p1.num_parcelamento in ( ".$this->getDado('num_parcelamento')." ) ";

    }

     $stSql.="                        GROUP BY cod_credito
                                    , cod_especie
                                    , cod_genero
                                    , cod_natureza
                                    , num_parcelamento
                           ) AS origem
                        ON origem.cod_parcela      = parcela_origem.cod_parcela
                       AND origem.cod_credito      = parcela_origem.cod_credito
                       AND origem.cod_especie      = parcela_origem.cod_especie
                       AND origem.cod_genero       = parcela_origem.cod_genero
                       AND origem.cod_natureza     = parcela_origem.cod_natureza
                       AND origem.num_parcelamento = minimo.num_parcelamento
                INNER JOIN monetario.credito
                        ON credito.cod_credito  = parcela_origem.cod_credito
                       AND credito.cod_especie  = parcela_origem.cod_especie
                       AND credito.cod_genero   = parcela_origem.cod_genero
                       AND credito.cod_natureza = parcela_origem.cod_natureza

                     WHERE parcela_origem.cod_parcela      = origem.cod_parcela
            ) AS origem
            ON origem.num_parcelamento = ddp.num_parcelamento
            AND origem.exercicio = dda.exercicio
            AND origem.cod_inscricao = dda.cod_inscricao

             /*  INNER JOIN ( SELECT sum(dpo.valor) AS valor
                                 , credito.descricao_credito
                                 , dpo.num_parcelamento
                              FROM divida.parcela_origem AS dpo
                        INNER JOIN monetario.credito
                                ON credito.cod_credito = dpo.cod_credito
                               AND credito.cod_especie = dpo.cod_especie
                               AND credito.cod_genero = dpo.cod_genero
                               AND credito.cod_natureza = dpo.cod_natureza
                          GROUP BY credito.descricao_credito
                                 , dpo.num_parcelamento
                          )AS origem
                       ON origem.num_parcelamento = ddp.num_parcelamento */
        ";

        return $stSql;
    }

    public function recuperaConsultaNotificacaoDivida(&$rsRecordSet, $stFiltro, $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stSql = $this->montaRecuperaConsultaNotificacaoDivida().$stFiltro;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaConsultaNotificacaoDivida()
    {
        $stSql   = "SELECT DISTINCT
                        (
                            SELECT
                                il.nom_localizacao
                            FROM
                                imobiliario.lote_localizacao AS ill

                            INNER JOIN
                                imobiliario.localizacao AS il
                            ON
                                il.cod_localizacao = ill.cod_localizacao

                            INNER JOIN
                                imobiliario.localizacao_nivel AS iln
                            ON
                                il.codigo_composto = iln.valor || '.00'
                                AND iln.cod_localizacao = ill.cod_localizacao
                                AND iln.cod_nivel = 1

                            WHERE
                                ill.cod_lote = imobiliario.fn_busca_lote_imovel(
                                    COALESCE(
                                        (
                                            SELECT
                                                domicilio_fiscal.inscricao_municipal
                                            FROM
                                                economico.domicilio_fiscal
                                            WHERE
                                                domicilio_fiscal.inscricao_economica = dde.inscricao_economica
                                        ),
                                        ddi.inscricao_municipal
                                    )
                                )
                        )AS regiao,

                        (
                            SELECT
                                il.nom_localizacao
                            FROM
                                imobiliario.lote_localizacao AS ill

                            INNER JOIN
                                imobiliario.localizacao AS il
                            ON
                                il.cod_localizacao = ill.cod_localizacao

                            WHERE
                                ill.cod_lote = imobiliario.fn_busca_lote_imovel(
                                    COALESCE(
                                        (
                                            SELECT
                                                domicilio_fiscal.inscricao_municipal
                                            FROM
                                                economico.domicilio_fiscal
                                            WHERE
                                                domicilio_fiscal.inscricao_economica = dde.inscricao_economica
                                        ),
                                        ddi.inscricao_municipal
                                    )
                                )
                        )AS distrito,

                        CASE WHEN ddi.inscricao_municipal IS NOT NULL THEN
                            ( select split_part ( imobiliario.fn_busca_endereco_imovel( ddi.inscricao_municipal ), '§', 2) )
                        ELSE
                            CASE WHEN dde.inscricao_economica IS NOT NULL THEN
                            split_part( COALESCE( economico.fn_busca_domicilio_fiscal( dde.inscricao_economica ), economico.fn_busca_domicilio_informado( dde.inscricao_economica ) ), '§', 2 )
                            ELSE
                                0::text
                            END
                        END AS cod_logradouro,

                        CASE WHEN ddi.inscricao_municipal IS NOT NULL THEN
                            ( select split_part ( imobiliario.fn_busca_endereco_imovel( ddi.inscricao_municipal ), '§', 9)||'/'||split_part ( imobiliario.fn_busca_endereco_imovel( ddi.inscricao_municipal ), '§', 11) )
                        END AS cidade_estado,

                        (
                            SELECT
                                sw_cgm.bairro
                            FROM
                                sw_cgm
                            WHERE
                                sw_cgm.numcgm = ddc.numcgm
                        )AS bairro_notificado,

                        (
                            SELECT
                                sw_cgm.cep
                            FROM
                                sw_cgm
                            WHERE
                                sw_cgm.numcgm = ddc.numcgm
                        )AS cep_notificado,

                        (
                            SELECT
                                sw_municipio.nom_municipio || '/' || sw_uf.nom_uf

                            FROM
                                sw_cgm

                            INNER JOIN
                                sw_uf
                            ON
                                sw_uf.cod_pais = sw_cgm.cod_pais
                                AND sw_uf.cod_uf = sw_cgm.cod_uf

                            INNER JOIN
                                sw_municipio
                            ON
                                sw_municipio.cod_municipio = sw_cgm.cod_municipio
                                AND sw_municipio.cod_uf = sw_cgm.cod_uf

                            WHERE
                                sw_cgm.numcgm = ddc.numcgm
                        )AS cidade_estado_notificado,


                        (
                            SELECT
                                sw_cgm.logradouro ||' '|| sw_cgm.numero ||' '|| sw_cgm.complemento
                            FROM
                                sw_cgm
                            WHERE
                                sw_cgm.numcgm = ddc.numcgm
                        )AS endereco_notificado,

                        (
                            SELECT
                                COALESCE( sw_cgm_pessoa_fisica.cpf, sw_cgm_pessoa_juridica.cnpj )

                            FROM
                                sw_cgm

                            LEFT JOIN
                                sw_cgm_pessoa_fisica
                            ON
                                sw_cgm_pessoa_fisica.numcgm = sw_cgm.numcgm

                            LEFT JOIN
                                sw_cgm_pessoa_juridica
                            ON
                                sw_cgm_pessoa_juridica.numcgm = sw_cgm.numcgm

                            WHERE
                                sw_cgm.numcgm = ddc.numcgm
                        )AS cpf_cnpj_notificado,
                        (
                            SELECT
                                COALESCE(
                                    (
                                        SELECT
                                            valor
                                        FROM
                                            imobiliario.atributo_lote_urbano_valor as ialu
                                        WHERE
                                            ialu.cod_atributo = 5
                                            AND  ialu.cod_lote = il.cod_lote
                                        ORDER BY
                                            ialu.timestamp
                                        DESC limit 1
                                    ),
                                    (
                                        SELECT
                                            valor
                                        FROM
                                            imobiliario.atributo_lote_rural_valor as ialr
                                        WHERE
                                            ialr.cod_atributo = 5
                                            AND ialr.cod_lote = il.cod_lote
                                        ORDER BY
                                            ialr.timestamp
                                        DESC limit 1
                                    )
                                )

                            FROM
                                imobiliario.lote as il

                            WHERE
                                il.cod_lote = imobiliario.fn_busca_lote_imovel(
                                    COALESCE(
                                        (
                                            SELECT
                                                domicilio_fiscal.inscricao_municipal
                                            FROM
                                                economico.domicilio_fiscal
                                            WHERE
                                                domicilio_fiscal.inscricao_economica = dde.inscricao_economica
                                        ),
                                        ddi.inscricao_municipal
                                        )
                                )
                        ) AS numero_quadra,

                        (
                            SELECT
                                COALESCE(
                                    (
                                        SELECT
                                            valor

                                        FROM
                                            imobiliario.atributo_lote_urbano_valor as ialu

                                        WHERE
                                            ialu.cod_atributo = 7
                                            AND ialu.cod_lote = il.cod_lote

                                        ORDER BY ialu.timestamp DESC limit 1
                                    ),
                                    (
                                        SELECT
                                            valor

                                        FROM
                                            imobiliario.atributo_lote_rural_valor as ialr

                                        WHERE
                                            ialr.cod_atributo = 7
                                            AND ialr.cod_lote = il.cod_lote

                                        ORDER BY ialr.timestamp DESC limit 1
                                    )
                                )
                            FROM
                                imobiliario.lote as il

                            WHERE
                                il.cod_lote = imobiliario.fn_busca_lote_imovel(
                                    COALESCE(
                                        (
                                            SELECT
                                                domicilio_fiscal.inscricao_municipal
                                            FROM
                                                economico.domicilio_fiscal
                                            WHERE
                                                domicilio_fiscal.inscricao_economica = dde.inscricao_economica
                                        ),
                                        ddi.inscricao_municipal
                                        )
                                )
                        ) AS numero_lote,

                        to_char(dda.dt_vencimento_origem, 'dd/mm/yyyy') AS dt_vencimento_origem,
                        CASE WHEN ddi.inscricao_municipal IS NOT NULL THEN
                            'im'
                        ELSE
                            CASE WHEN dde.inscricao_economica IS NOT NULL THEN
                                'ie'
                            ELSE
                                'cgm'
                            END
                        END AS tipo_inscricao,
                        ddi.inscricao_municipal,
                        dde.inscricao_economica,
                        dda.cod_inscricao,
                        dda.exercicio,
                        dda.num_livro,
                        dda.num_folha,
                        COALESCE( ddi.inscricao_municipal, dde.inscricao_economica, ddc.numcgm) AS inscricao,
                        (
                            SELECT
                                sum(dpo.valor)

                            FROM
                                divida.parcela_origem AS dpo

                            WHERE
                                dpo.num_parcelamento = (
                                    SELECT
                                        divida.divida_parcelamento.num_parcelamento
                                    FROM
                                        divida.divida_parcelamento
                                    WHERE
                                        divida.divida_parcelamento.cod_inscricao = dda.cod_inscricao
                                        AND divida.divida_parcelamento.exercicio = dda.exercicio
                                    ORDER BY
                                        divida.divida_parcelamento.num_parcelamento ASC
                                    LIMIT 1
                                )
                                AND dpo.cod_parcela IN (
                                    SELECT
                                        dpo2.cod_parcela
                                    FROM
                                        divida.parcela_origem AS dpo2
                                    WHERE
                                        dpo2.num_parcelamento = ddp.num_parcelamento
                                        AND dpo2.cod_parcela = dpo.cod_parcela
                                )
                        )AS valor_origem,

                        split_part(
                            aplica_acrescimo_modalidade(
                                dpar.cod_modalidade,
                                3,
                                dpar.num_parcelamento,
                                COALESCE( (
                                    SELECT
                                        sum(dpo.valor)
                                    FROM
                                        divida.parcela_origem AS dpo
                                    WHERE
                                        dpo.num_parcelamento = (
                                            SELECT
                                                divida.divida_parcelamento.num_parcelamento
                                            FROM
                                                divida.divida_parcelamento
                                            WHERE
                                                divida.divida_parcelamento.cod_inscricao = dda.cod_inscricao
                                                AND divida.divida_parcelamento.exercicio = dda.exercicio
                                            ORDER BY
                                                divida.divida_parcelamento.num_parcelamento ASC
                                            LIMIT 1
                                        )
                                        AND dpo.cod_parcela IN (
                                            SELECT
                                                dpo2.cod_parcela
                                            FROM
                                                divida.parcela_origem AS dpo2
                                            WHERE
                                                dpo2.num_parcelamento = ddp.num_parcelamento
                                                AND dpo2.cod_parcela = dpo.cod_parcela
                                        )
                                ), 0.00),
                                dda.dt_vencimento_origem,
                                to_char( dpar.timestamp, 'yyyy-mm-dd' )::date
                            ),
                        ';',
                            5
                        ) AS multa_infracao,
                        split_part(
                            aplica_acrescimo_modalidade(
                                dpar.cod_modalidade,
                                3,
                                dpar.num_parcelamento,
                                COALESCE( (
                                    SELECT
                                        sum(dpo.valor)
                                    FROM
                                        divida.parcela_origem AS dpo
                                    WHERE
                                        dpo.num_parcelamento = (
                                            SELECT
                                                divida.divida_parcelamento.num_parcelamento
                                            FROM
                                                divida.divida_parcelamento
                                            WHERE
                                                divida.divida_parcelamento.cod_inscricao = dda.cod_inscricao
                                                AND divida.divida_parcelamento.exercicio = dda.exercicio
                                            ORDER BY
                                                divida.divida_parcelamento.num_parcelamento ASC
                                            LIMIT 1
                                        )
                                        AND dpo.cod_parcela IN (
                                            SELECT
                                                dpo2.cod_parcela
                                            FROM
                                                divida.parcela_origem AS dpo2
                                            WHERE
                                                dpo2.num_parcelamento = ddp.num_parcelamento
                                                AND dpo2.cod_parcela = dpo.cod_parcela
                                        )
                                ), 0.00),
                                dda.dt_vencimento_origem,
                                to_char( dpar.timestamp, 'yyyy-mm-dd' )::date
                            ),
                        ';',
                            2
                        ) AS multa,
                        split_part(
                            aplica_acrescimo_modalidade(
                                dpar.cod_modalidade,
                                2,
                                dpar.num_parcelamento,
                                COALESCE( (
                                    SELECT
                                        sum(dpo.valor)
                                    FROM
                                        divida.parcela_origem AS dpo
                                    WHERE
                                        dpo.num_parcelamento = (
                                            SELECT
                                                divida.divida_parcelamento.num_parcelamento
                                            FROM
                                                divida.divida_parcelamento
                                            WHERE
                                                divida.divida_parcelamento.cod_inscricao = dda.cod_inscricao
                                                AND divida.divida_parcelamento.exercicio = dda.exercicio
                                            ORDER BY
                                                divida.divida_parcelamento.num_parcelamento ASC
                                            LIMIT 1
                                        )
                                        AND dpo.cod_parcela IN (
                                            SELECT
                                                dpo2.cod_parcela
                                            FROM
                                                divida.parcela_origem AS dpo2
                                            WHERE
                                                dpo2.num_parcelamento = ddp.num_parcelamento
                                                AND dpo2.cod_parcela = dpo.cod_parcela
                                        )
                                ), 0.00 ),
                                dda.dt_vencimento_origem,
                                to_char( dpar.timestamp, 'yyyy-mm-dd' )::date
                            ),
                            ';',
                            1
                        ) AS juros,
                        split_part(
                            aplica_acrescimo_modalidade(
                                dpar.cod_modalidade,
                                1,
                                dpar.num_parcelamento,
                                COALESCE( (
                                    SELECT
                                        sum(dpo.valor)
                                    FROM
                                        divida.parcela_origem AS dpo
                                    WHERE
                                        dpo.num_parcelamento = (
                                            SELECT
                                                divida.divida_parcelamento.num_parcelamento
                                            FROM
                                                divida.divida_parcelamento
                                            WHERE
                                                divida.divida_parcelamento.cod_inscricao = dda.cod_inscricao
                                                AND divida.divida_parcelamento.exercicio = dda.exercicio
                                            ORDER BY
                                                divida.divida_parcelamento.num_parcelamento ASC
                                            LIMIT 1
                                        )
                                        AND dpo.cod_parcela IN (
                                            SELECT
                                                dpo2.cod_parcela
                                            FROM
                                                divida.parcela_origem AS dpo2
                                            WHERE
                                                dpo2.num_parcelamento = ddp.num_parcelamento
                                                AND dpo2.cod_parcela = dpo.cod_parcela
                                        )
                                ), 0.00),
                                dda.dt_vencimento_origem,
                                to_char( dpar.timestamp, 'yyyy-mm-dd' )::date
                            ),
                            ';',
                            1
                        ) AS correcao,

                        (
                            SELECT
                                sum(valor)
                            FROM
                                divida.parcela_reducao
                            WHERE
                                divida.parcela_reducao.num_parcelamento = ddp.num_parcelamento
                        )AS total_reducao,
                        dda.exercicio_original AS exercicio_origem,
                        (
                            SELECT
                                (
                                    SELECT
                                        arrecadacao.fn_busca_origem_lancamento ( ap.cod_lancamento, dda.exercicio_original, 1, 1 )
                                    FROM
                                        arrecadacao.parcela AS ap
                                    WHERE
                                        ap.cod_parcela = dpo.cod_parcela
                                )
                            FROM
                                divida.parcela_origem AS dpo
                            WHERE
                                dpo.num_parcelamento = (
                                    SELECT
                                        divida.divida_parcelamento.num_parcelamento
                                    FROM
                                        divida.divida_parcelamento
                                    WHERE
                                        divida.divida_parcelamento.cod_inscricao = dda.cod_inscricao
                                        AND divida.divida_parcelamento.exercicio = dda.exercicio
                                    ORDER BY
                                        divida.divida_parcelamento.num_parcelamento ASC
                                    LIMIT 1
                                )
                                AND dpo.cod_parcela IN (
                                    SELECT
                                        dpo2.cod_parcela
                                    FROM
                                        divida.parcela_origem AS dpo2
                                    WHERE
                                        dpo2.num_parcelamento = ddp.num_parcelamento
                                        AND dpo2.cod_parcela = dpo.cod_parcela
                                )
                                LIMIT 1
                        )AS imposto,

                        (
                            SELECT
                                nom_cgm
                            FROM
                                sw_cgm
                            WHERE
                                sw_cgm.numcgm = ddc.numcgm
                        )AS nome_notificado,

                        CASE WHEN ddi.inscricao_municipal IS NOT NULL THEN
                            ( select split_part ( imobiliario.fn_busca_endereco_imovel( ddi.inscricao_municipal ), '§', 1)||' '||split_part ( imobiliario.fn_busca_endereco_imovel( ddi.inscricao_municipal ), '§', 3)||', '||split_part ( imobiliario.fn_busca_endereco_imovel( ddi.inscricao_municipal ), '§', 4) )
                        ELSE
                            CASE WHEN dde.inscricao_economica IS NOT NULL THEN
                                (select split_part ( COALESCE( economico.fn_busca_domicilio_fiscal( dde.inscricao_economica ), economico.fn_busca_domicilio_informado( dde.inscricao_economica ) ), '§', 1 ))||' '||(select split_part ( COALESCE( economico.fn_busca_domicilio_fiscal( dde.inscricao_economica ), economico.fn_busca_domicilio_informado( dde.inscricao_economica ) ), '§', 3 ))||', '||(select split_part ( COALESCE( economico.fn_busca_domicilio_fiscal( dde.inscricao_economica ), economico.fn_busca_domicilio_informado( dde.inscricao_economica ) ), '§', 4 ))
                            ELSE
                                (
                                    SELECT
                                        sw_cgm.logradouro ||' '|| sw_cgm.numero ||' '|| sw_cgm.complemento
                                    FROM
                                        sw_cgm
                                    WHERE
                                        sw_cgm.numcgm = ddc.numcgm
                                )
                            END
                        END AS domicilio_fiscal,
                        dpar.num_parcelamento


                    FROM
                        divida.divida_ativa AS dda

                    INNER JOIN
                        divida.divida_cgm AS ddc
                    ON
                        ddc.cod_inscricao = dda.cod_inscricao
                        AND ddc.exercicio = dda.exercicio

                    LEFT JOIN
                        divida.divida_imovel AS ddi
                    ON
                        ddi.cod_inscricao = ddc.cod_inscricao
                        AND ddi.exercicio = ddc.exercicio

                    LEFT JOIN
                        divida.divida_empresa AS dde
                    ON
                        dde.cod_inscricao = ddc.cod_inscricao
                        AND dde.exercicio = ddc.exercicio

                    INNER JOIN
                        (
                            SELECT
                                divida_parcelamento.cod_inscricao,
                                divida_parcelamento.exercicio,
                                max(divida_parcelamento.num_parcelamento) AS num_parcelamento
                            FROM
                                divida.divida_parcelamento
                            GROUP BY
                                divida_parcelamento.cod_inscricao,
                                divida_parcelamento.exercicio
                        )AS ddp
                    ON
                        ddp.cod_inscricao = ddc.cod_inscricao
                        AND ddp.exercicio = ddc.exercicio

                    INNER JOIN
                        divida.parcelamento AS dpar
                    ON
                        dpar.num_parcelamento = ddp.num_parcelamento \n";

        return $stSql;
    }

    public function recuperaConsultaNotificacaoDividaNormas(&$rsRecordSet, $stNumParcelamento, $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stSql = $this->montaRecuperaConsultaNotificacaoDividaNormas($stNumParcelamento);
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaConsultaNotificacaoDividaNormas($stNumParcelamento)
    {
        $stSql   = "
            SELECT
                DISTINCT
                CASE WHEN dpo.cod_norma IS NOT NULL AND dpo.norma_imposto IS NOT NULL THEN
                            dpo.descricao_grupo_credito||' '|| dpo.exercicio_grupo_credito || ' - ' || dpo.norma_imposto
                        WHEN dpo.cod_norma IS NOT NULL AND dpo.norma_imposto IS NULL THEN
                            dpo.descricao_grupo_credito||' '|| dpo.exercicio_grupo_credito ||' - ' || dpo.cod_norma
                        ELSE
                            dpo.descricao_grupo_credito||' '|| dpo.exercicio_grupo_credito
                        END AS descricao
                , dpo.exercicio_grupo_credito
          FROM divida.divida_parcelamento AS ddp
    INNER JOIN ( SELECT COALESCE( acgc.ano_exercicio, ac.exercicio ) AS exercicio
                      , credito.descricao_credito AS descricao_grupo_credito
                      , ac.exercicio AS exercicio_grupo_credito
                      , ( SELECT mcn.cod_norma
                            FROM monetario.credito_norma AS mcn
                           WHERE mcn.cod_credito = ac.cod_credito
                             AND mcn.cod_natureza = ac.cod_natureza
                             AND mcn.cod_genero = ac.cod_genero
                             AND mcn.cod_especie = ac.cod_especie
                             AND mcn.dt_inicio_vigencia <= ( SELECT min(divida_ativa.dt_vencimento_origem)
                                                               FROM divida.divida_parcelamento
                                                         INNER JOIN divida.divida_ativa
                                                                 ON divida_ativa.cod_inscricao = divida_parcelamento.cod_inscricao
                                                                AND divida_ativa.exercicio = divida_parcelamento.exercicio
                                                              WHERE divida_parcelamento.num_parcelamento = dpo.num_parcelamento
                                                            )
                        ORDER BY mcn.dt_inicio_vigencia DESC
                           LIMIT 1
                        )AS cod_norma,
                        ( SELECT tn.nom_tipo_norma||' '||nn.num_norma||'/'||nn.exercicio||' - '||nn.nom_norma
                            FROM monetario.credito_norma AS mcn
                      INNER JOIN normas.norma AS nn
                              ON nn.cod_norma = mcn.cod_norma
                      INNER JOIN normas.tipo_norma as tn
                              ON tn.cod_tipo_norma = nn.cod_tipo_norma
                           WHERE mcn.cod_credito = ac.cod_credito
                             AND mcn.cod_natureza = ac.cod_natureza
                             AND mcn.cod_genero = ac.cod_genero
                             AND mcn.cod_especie = ac.cod_especie
                             AND mcn.dt_inicio_vigencia <= ( SELECT min(divida_ativa.dt_vencimento_origem)
                                                               FROM divida.divida_parcelamento
                                                         INNER JOIN divida.divida_ativa
                                                                 ON divida_ativa.cod_inscricao = divida_parcelamento.cod_inscricao
                                                                AND divida_ativa.exercicio = divida_parcelamento.exercicio
                                                              WHERE divida_parcelamento.num_parcelamento = dpo.num_parcelamento
                                                            )
                        ORDER BY mcn.dt_inicio_vigencia DESC
                           LIMIT 1
                        )AS norma_imposto
                      , dpo.num_parcelamento
                   FROM ( SELECT dpo.num_parcelamento
                               , dpo.cod_credito
                               , dpo.cod_especie
                               , dpo.cod_genero
                               , dpo.cod_natureza
                               , ap.cod_lancamento
                            FROM divida.parcela_origem AS dpo
                      INNER JOIN arrecadacao.parcela AS ap
                              ON ap.cod_parcela = dpo.cod_parcela
                         WHERE dpo.num_parcelamento in ( ".$stNumParcelamento." )
                        GROUP BY dpo.num_parcelamento
                               , dpo.cod_credito
                               , dpo.cod_especie
                               , dpo.cod_genero
                               , dpo.cod_natureza
                               , ap.cod_lancamento
                        )AS dpo
             INNER JOIN arrecadacao.lancamento_calculo AS alc
                     ON alc.cod_lancamento = dpo.cod_lancamento
             INNER JOIN arrecadacao.calculo AS ac
                     ON ac.cod_calculo = alc.cod_calculo
                    AND ac.cod_credito = dpo.cod_credito
                    AND ac.cod_especie = dpo.cod_especie
                    AND ac.cod_genero = dpo.cod_genero
                    AND ac.cod_natureza = dpo.cod_natureza
             INNER JOIN monetario.credito
                     ON ac.cod_credito = credito.cod_credito
                    AND ac.cod_especie = credito.cod_especie
                    AND ac.cod_genero = credito.cod_genero
                    AND ac.cod_natureza = credito.cod_natureza
              LEFT JOIN arrecadacao.calculo_grupo_credito AS acgc
                     ON acgc.cod_calculo = alc.cod_calculo
               )AS dpo
            ON dpo.num_parcelamento = ddp.num_parcelamento
         WHERE ddp.num_parcelamento in ( ".$stNumParcelamento." )
      ORDER BY dpo.exercicio_grupo_credito DESC ";

        return $stSql;
    }

    public function recuperaConsultaNotificacaoAcordo(&$rsRecordSet, $stFiltro, $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stSql = $this->montaRecuperaConsultaNotificacaoAcordo().$stFiltro;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaConsultaNotificacaoAcordo()
    {
        $stSql   = "SELECT DISTINCT
                        tdpa.total,
                        to_char(now(), 'dd/mm/yyyy') AS dt_notificacao,
                        ddi.inscricao_municipal,

                        (
                            SELECT
                                COALESCE( sw_cgm_pessoa_fisica.cpf, sw_cgm_pessoa_juridica.cnpj )

                            FROM
                                sw_cgm

                            LEFT JOIN
                                sw_cgm_pessoa_fisica
                            ON
                                sw_cgm_pessoa_fisica.numcgm = sw_cgm.numcgm

                            LEFT JOIN
                                sw_cgm_pessoa_juridica
                            ON
                                sw_cgm_pessoa_juridica.numcgm = sw_cgm.numcgm

                            WHERE
                                sw_cgm.numcgm = ddc.numcgm
                        )AS cpf_cnpj,

                        CASE WHEN ddi.inscricao_municipal IS NOT NULL THEN
                            'im'
                        ELSE
                            CASE WHEN dde.inscricao_economica IS NOT NULL THEN
                                'ie'
                            ELSE
                                'cgm'
                            END
                        END AS tipo_inscricao,

                        CASE WHEN ddi.inscricao_municipal IS NOT NULL THEN
                            ( select split_part ( imobiliario.fn_busca_endereco_imovel( ddi.inscricao_municipal ), '§', 1)||' '||split_part ( imobiliario.fn_busca_endereco_imovel( ddi.inscricao_municipal ), '§', 3)||', '||split_part ( imobiliario.fn_busca_endereco_imovel( ddi.inscricao_municipal ), '§', 4) )
                        ELSE
                            CASE WHEN dde.inscricao_economica IS NOT NULL THEN
                            COALESCE( economico.fn_busca_domicilio_fiscal( dde.inscricao_economica ), economico.fn_busca_domicilio_informado( dde.inscricao_economica ) )
                            ELSE
                                (
                                    SELECT
                                        sw_cgm.logradouro ||' '|| sw_cgm.numero ||' '|| sw_cgm.complemento
                                    FROM
                                        sw_cgm
                                    WHERE
                                        sw_cgm.numcgm = ddc.numcgm
                                )
                            END
                        END AS domicilio_fiscal,

                        (
                            SELECT
                                sw_cgm.nom_cgm
                            FROM
                                sw_cgm
                            WHERE
                                sw_cgm.numcgm = ddc.numcgm
                        )AS contribuinte,

                        dde.inscricao_economica,

                        dpa.num_parcela,
                        dpa.num_parcela || '/' || tdpa.total AS parcelas,
                        dpa.vlr_parcela,
                        to_char(dpa.dt_vencimento_parcela, 'dd/mm/yyyy') AS dt_vencimento,
                        to_char(dp.timestamp, 'dd/mm/yyyy') AS dt_acordo,
                        CASE WHEN paga = true THEN
                            'Paga'
                        ELSE
                            CASE WHEN ( dpa.dt_vencimento_parcela < now() ) THEN
                                'Vencida'
                            ELSE
                                'Sem Pagamento'
                            END
                        END AS situacao,

                        dp.numero_parcelamento ||'/'||dp.exercicio AS nr_acordo_administrativo,
                        (
                            SELECT
                                num_documento||'/'||exercicio
                            FROM
                                divida.emissao_documento
                            WHERE
                                emissao_documento.num_parcelamento = dp.num_parcelamento
                                AND emissao_documento.exercicio = dp.exercicio
                                AND cod_tipo_documento = 4
                            ORDER BY
                                timestamp DESC
                            LIMIT 1
                        )as notificacao_nr,
                        calculo.valor AS valor_corrigido,
                        total_multa.valor AS valor_multa,
                        total_correcao.valor AS valor_correcao,
                        total_juros.valor AS valor_juros,
                        total_reducao.valor AS valor_reducao,
                        pagamento.valor AS valor_pago,
                        pagamento.dt_pagamento
                    FROM
                        divida.divida_ativa AS dda

                    INNER JOIN
                        divida.divida_cgm AS ddc
                    ON
                        ddc.cod_inscricao = dda.cod_inscricao
                        AND ddc.exercicio = dda.exercicio

                    LEFT JOIN
                        divida.divida_imovel AS ddi
                    ON
                        ddi.cod_inscricao = dda.cod_inscricao
                        AND ddi.exercicio = dda.exercicio

                    LEFT JOIN
                        divida.divida_empresa AS dde
                    ON
                        dde.cod_inscricao = dda.cod_inscricao
                        AND dde.exercicio = dda.exercicio

                    INNER JOIN
                        divida.divida_parcelamento AS ddp
                    ON
                        ddp.cod_inscricao = dda.cod_inscricao
                        AND ddp.exercicio = dda.exercicio

                    INNER JOIN
                        divida.parcelamento AS dp
                    ON
                        dp.num_parcelamento = ddp.num_parcelamento

                    INNER JOIN
                        (
                            SELECT
                                count(num_parcela) AS total,
                                num_parcelamento
                            FROM
                                divida.parcela
                            GROUP BY
                                num_parcelamento
                        )AS tdpa
                    ON
                        tdpa.num_parcelamento = ddp.num_parcelamento

                    INNER JOIN
                        divida.parcela AS dpa
                    ON
                        dpa.num_parcelamento = ddp.num_parcelamento

                    LEFT JOIN ( SELECT parcela_calculo.num_parcelamento
                                     , parcela_calculo.num_parcela
                                     , pagamento.valor
                                     , to_char( pagamento.data_pagamento, 'dd/mm/yyyy' ) AS dt_pagamento
                                  FROM divida.parcela_calculo
                            INNER JOIN arrecadacao.lancamento_calculo
                                    ON lancamento_calculo.cod_calculo = parcela_calculo.cod_calculo
                            INNER JOIN arrecadacao.parcela
                                    ON parcela.nr_parcela = parcela_calculo.num_parcela
                                   AND parcela.cod_lancamento = lancamento_calculo.cod_lancamento
                            INNER JOIN arrecadacao.carne
                                    ON carne.cod_parcela = parcela.cod_parcela
                            INNER JOIN arrecadacao.pagamento
                                    ON pagamento.numeracao = carne.numeracao
                              )AS pagamento
                            ON pagamento.num_parcelamento = ddp.num_parcelamento
                           AND pagamento.num_parcela = dpa.num_parcela

                    INNER JOIN ( SELECT sum(vl_credito) AS valor
                                      , num_parcelamento
                                   FROM divida.parcela_calculo
                               GROUP BY num_parcelamento
                               )AS calculo
                            ON calculo.num_parcelamento = ddp.num_parcelamento

                    LEFT JOIN ( SELECT sum(vlracrescimo) AS valor
                                     , num_parcelamento
                                  FROM divida.parcela_acrescimo
                                 WHERE cod_tipo = 1
                              GROUP BY num_parcelamento
                              )AS total_correcao
                           ON total_correcao.num_parcelamento = ddp.num_parcelamento

                    LEFT JOIN ( SELECT sum(vlracrescimo) AS valor
                                     , num_parcelamento
                                  FROM divida.parcela_acrescimo
                                 WHERE cod_tipo = 2
                              GROUP BY num_parcelamento
                              )AS total_juros
                           ON total_juros.num_parcelamento = ddp.num_parcelamento

                    LEFT JOIN ( SELECT sum(vlracrescimo) AS valor
                                     , num_parcelamento
                                  FROM divida.parcela_acrescimo
                                 WHERE cod_tipo = 3
                              GROUP BY num_parcelamento
                              )AS total_multa
                           ON total_multa.num_parcelamento = ddp.num_parcelamento

                    LEFT JOIN ( SELECT sum(valor) AS valor
                                     , num_parcelamento
                                  FROM divida.parcela_reducao
                              GROUP BY num_parcelamento
                              )AS total_reducao
                           ON total_reducao.num_parcelamento = ddp.num_parcelamento
        \n";

        return $stSql;
    }

    public function recuperaConsultaTermoParcelamento(&$rsRecordSet, $stFiltro, $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stSql = $this->montaRecuperaConsultaTermoParcelamento().$stFiltro;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaConsultaTermoParcelamento()
    {
        $stSql = " SELECT DISTINCT
                        (
                            SELECT  il.nom_localizacao
                              FROM  imobiliario.lote_localizacao AS ill
                        INNER JOIN  imobiliario.localizacao AS il
                                ON  il.cod_localizacao = ill.cod_localizacao
                        INNER JOIN  imobiliario.localizacao_nivel AS iln
                                ON  il.codigo_composto = iln.valor || '.00'
                               AND iln.cod_localizacao = ill.cod_localizacao
                               AND iln.cod_nivel = 1

                            WHERE
                                ill.cod_lote = imobiliario.fn_busca_lote_imovel(
                                    COALESCE(
                                        (
                                            SELECT  domicilio_fiscal.inscricao_municipal
                                              FROM  economico.domicilio_fiscal
                                             WHERE  domicilio_fiscal.inscricao_economica = dde.inscricao_economica
                                          ORDER BY  domicilio_fiscal.timestamp DESC
                                             LIMIT  1
                                        ),
                                        ddi.inscricao_municipal
                                    )
                                )
                        )AS regiao,

                        (
                            SELECT  il.nom_localizacao
                              FROM  imobiliario.lote_localizacao AS ill
                        INNER JOIN  imobiliario.localizacao AS il
                                ON  il.cod_localizacao = ill.cod_localizacao
                             WHERE  ill.cod_lote = imobiliario.fn_busca_lote_imovel(
                                    COALESCE(
                                        (
                                            SELECT  domicilio_fiscal.inscricao_municipal
                                              FROM  economico.domicilio_fiscal
                                             WHERE  domicilio_fiscal.inscricao_economica = dde.inscricao_economica
                                          ORDER BY  domicilio_fiscal.timestamp DESC
                                             LIMIT  1
                                        ),
                                        ddi.inscricao_municipal
                                    )
                                )
                        )AS distrito,

                        CASE WHEN ddi.inscricao_municipal IS NOT NULL THEN
                            ( select split_part ( imobiliario.fn_busca_endereco_imovel( ddi.inscricao_municipal ), '§', 2) )
                        ELSE
                            CASE WHEN dde.inscricao_economica IS NOT NULL THEN
                            split_part( COALESCE( economico.fn_busca_domicilio_fiscal( dde.inscricao_economica ), economico.fn_busca_domicilio_informado( dde.inscricao_economica ) ), '§', 2 )
                            ELSE
                                0::text
                            END
                        END AS cod_logradouro,

                        CASE WHEN ddi.inscricao_municipal IS NOT NULL THEN
                            ( select split_part ( imobiliario.fn_busca_endereco_imovel( ddi.inscricao_municipal ), '§', 9)||'/'||split_part ( imobiliario.fn_busca_endereco_imovel( ddi.inscricao_municipal ), '§', 11) )
                        END AS cidade_estado,

                        (
                            SELECT  sw_cgm.bairro
                              FROM  sw_cgm
                             WHERE  sw_cgm.numcgm = ddc.numcgm
                        ) AS bairro_notificado,

                        (
                            SELECT  COALESCE( sw_cgm_pessoa_fisica.rg, sw_cgm_pessoa_juridica.insc_estadual )
                              FROM  sw_cgm
                         LEFT JOIN  sw_cgm_pessoa_fisica
                                ON  sw_cgm_pessoa_fisica.numcgm = ddc.numcgm
                         LEFT JOIN  sw_cgm_pessoa_juridica
                                ON  sw_cgm_pessoa_juridica.numcgm = ddc.numcgm
                             WHERE  sw_cgm.numcgm = ddc.numcgm
                        ) AS rg_insc_estadual,

                        (
                            SELECT  sw_cgm.cep
                              FROM  sw_cgm
                             WHERE  sw_cgm.numcgm = ddc.numcgm
                        ) AS cep_notificado,

                        (
                            SELECT  sw_cgm.fone_residencial
                              FROM  sw_cgm
                             WHERE  sw_cgm.numcgm = ddc.numcgm
                        ) AS telefone_notificado,

                        (
                            SELECT  sw_municipio.nom_municipio || '/' || sw_uf.nom_uf
                              FROM  sw_cgm
                        INNER JOIN  sw_uf
                                ON  sw_uf.cod_pais = sw_cgm.cod_pais
                               AND  sw_uf.cod_uf = sw_cgm.cod_uf
                        INNER JOIN  sw_municipio
                                ON  sw_municipio.cod_municipio = sw_cgm.cod_municipio
                               AND  sw_municipio.cod_uf = sw_cgm.cod_uf
                             WHERE  sw_cgm.numcgm = ddc.numcgm
                        ) AS cidade_estado_notificado,

                        (
                            SELECT  sw_cgm.logradouro ||' '|| sw_cgm.numero ||' '|| sw_cgm.complemento
                              FROM  sw_cgm
                             WHERE  sw_cgm.numcgm = ddc.numcgm
                        ) AS endereco_notificado,

                        (
                            SELECT  COALESCE( sw_cgm_pessoa_fisica.cpf, sw_cgm_pessoa_juridica.cnpj )
                              FROM  sw_cgm
                         LEFT JOIN  sw_cgm_pessoa_fisica
                                ON  sw_cgm_pessoa_fisica.numcgm = sw_cgm.numcgm
                         LEFT JOIN  sw_cgm_pessoa_juridica
                                ON  sw_cgm_pessoa_juridica.numcgm = sw_cgm.numcgm
                             WHERE  sw_cgm.numcgm = ddc.numcgm
                        ) AS cpf_cnpj_notificado,

                        (
                            SELECT
                                COALESCE(
                                    (
                                        SELECT  valor
                                          FROM  imobiliario.atributo_lote_urbano_valor as ialu
                                         WHERE  ialu.cod_atributo = 5
                                           AND  ialu.cod_lote = il.cod_lote
                                      ORDER BY  ialu.timestamp DESC
                                         LIMIT  1
                                    ),
                                    (
                                        SELECT  valor
                                          FROM  imobiliario.atributo_lote_rural_valor as ialr
                                         WHERE  ialr.cod_atributo = 5
                                           AND  ialr.cod_lote = il.cod_lote
                                      ORDER BY  ialr.timestamp DESC
                                         LIMIT  1
                                    )
                                )

                            FROM
                                imobiliario.lote as il

                            WHERE
                                il.cod_lote = imobiliario.fn_busca_lote_imovel(
                                    COALESCE(
                                        (
                                            SELECT  domicilio_fiscal.inscricao_municipal
                                              FROM  economico.domicilio_fiscal
                                             WHERE  domicilio_fiscal.inscricao_economica = dde.inscricao_economica
                                          ORDER BY  domicilio_fiscal.timestamp DESC
                                             LIMIT  1
                                        ),
                                        ddi.inscricao_municipal
                                        )
                                )
                        ) AS numero_quadra,

                        (
                            SELECT
                                COALESCE(
                                    (
                                        SELECT  valor
                                          FROM  imobiliario.atributo_lote_urbano_valor as ialu
                                         WHERE  ialu.cod_atributo = 7
                                           AND  ialu.cod_lote = il.cod_lote
                                      ORDER BY ialu.timestamp DESC limit 1
                                    ),
                                    (
                                        SELECT  valor
                                          FROM  imobiliario.atributo_lote_rural_valor as ialr
                                         WHERE  ialr.cod_atributo = 7
                                           AND  ialr.cod_lote = il.cod_lote
                                      ORDER BY ialr.timestamp DESC limit 1
                                    )
                                )

                            FROM  imobiliario.lote as il

                            WHERE  il.cod_lote = imobiliario.fn_busca_lote_imovel(
                                    COALESCE(
                                        (
                                            SELECT  domicilio_fiscal.inscricao_municipal
                                              FROM  economico.domicilio_fiscal
                                             WHERE  domicilio_fiscal.inscricao_economica = dde.inscricao_economica
                                          ORDER BY  domicilio_fiscal.timestamp DESC
                                             LIMIT  1
                                        ),
                                        ddi.inscricao_municipal
                                        )
                                )
                        ) AS numero_lote,
                        CASE WHEN ddi.inscricao_municipal IS NOT NULL THEN
                            'im'
                        ELSE
                            CASE WHEN dde.inscricao_economica IS NOT NULL THEN
                                'ie'
                            ELSE  'cgm'
                            END
                        END AS tipo_inscricao,

                        ddi.inscricao_municipal,
                        dde.inscricao_economica,
                        dda.cod_inscricao,
                        dda.exercicio AS exercicio_divida,
                        COALESCE( ddi.inscricao_municipal, dde.inscricao_economica, ddc.numcgm) AS inscricao,

                        (
                            SELECT  divida.fn_busca_origem_e_exercicio_num_parcelamento_para_livro ( divida.divida_parcelamento.num_parcelamento )
                              FROM  divida.divida_parcelamento
                             WHERE  divida.divida_parcelamento.cod_inscricao = dda.cod_inscricao
                               AND  divida.divida_parcelamento.exercicio = dda.exercicio
                          ORDER BY  divida.divida_parcelamento.num_parcelamento ASC
                             LIMIT 1
                        ) AS imposto,

                        (
                            SELECT  nom_cgm
                              FROM  sw_cgm
                             WHERE  sw_cgm.numcgm = ddc.numcgm
                        ) AS nome_notificado,

                        CASE WHEN ddi.inscricao_municipal IS NOT NULL THEN
                            ( select split_part ( imobiliario.fn_busca_endereco_imovel( ddi.inscricao_municipal ), '§', 1)||' '||split_part ( imobiliario.fn_busca_endereco_imovel( ddi.inscricao_municipal ), '§', 3)||', '||split_part ( imobiliario.fn_busca_endereco_imovel( ddi.inscricao_municipal ), '§', 4) )
                        ELSE
                            CASE WHEN dde.inscricao_economica IS NOT NULL THEN
                                (select split_part ( COALESCE( economico.fn_busca_domicilio_fiscal( dde.inscricao_economica ), economico.fn_busca_domicilio_informado( dde.inscricao_economica ) ), '§', 1 ))||' '||(select split_part ( COALESCE( economico.fn_busca_domicilio_fiscal( dde.inscricao_economica ), economico.fn_busca_domicilio_informado( dde.inscricao_economica ) ), '§', 3 ))||', '||(select split_part ( COALESCE( economico.fn_busca_domicilio_fiscal( dde.inscricao_economica ), economico.fn_busca_domicilio_informado( dde.inscricao_economica ) ), '§', 4 ))
                            ELSE
                                (
                                    SELECT  sw_cgm.logradouro ||' '|| sw_cgm.numero ||' '|| sw_cgm.complemento
                                      FROM  sw_cgm
                                     WHERE  sw_cgm.numcgm = ddc.numcgm
                                )
                            END
                        END AS domicilio_fiscal,
                        dpar.num_parcelamento,
                        dpar.numero_parcelamento,
                        dpar.numero_parcelamento ||'/'||dpar.exercicio AS nr_acordo_administrativo,
                        dpar.exercicio AS exercicio_cobranca,

                        (
                            SELECT  sum(parcela.vlr_parcela)
                              FROM  divida.parcela
                             WHERE  parcela.num_parcelamento = dpar.num_parcelamento
                        ) AS total_pagar,

                        (
                            SELECT  sum(parcela.vlr_parcela) + (

                                    SELECT  sum(parcela_reducao.valor)
                                      FROM  divida.parcela_reducao
                                     WHERE  parcela_reducao.num_parcelamento = dpar.num_parcelamento
                                    )
                              FROM  divida.parcela
                             WHERE  parcela.num_parcelamento = dpar.num_parcelamento

                        ) AS tpsd, --total a pagar sem descontos

                        (
                            SELECT  parcela.vlr_parcela
                              FROM  divida.parcela
                             WHERE  parcela.num_parcelamento = dpar.num_parcelamento
                               AND parcela.num_parcela = 1
                        ) AS valor_parcela,

                        (
                            SELECT  to_char(parcela.dt_vencimento_parcela, 'dd/mm/yyyy')
                              FROM  divida.parcela
                             WHERE  parcela.num_parcelamento = dpar.num_parcelamento
                               AND  parcela.num_parcela = 1
                        ) AS dt_vencimento_parcela,

                        (
                            SELECT  count(parcela)
                              FROM  divida.parcela
                             WHERE  parcela.num_parcelamento = dpar.num_parcelamento
                               AND  parcela.num_parcela > 0
                        ) AS parcelas,

                        (
                            SELECT  count(parcela)
                              FROM  divida.parcela
                             WHERE  parcela.num_parcelamento = dpar.num_parcelamento
                               AND parcela.num_parcela > 1
                        ) AS parcelas_menos,

                        (
                            SELECT
                                100.00
                                /
                                (
                                    SELECT  CASE WHEN ( count(parcela) = 0 ) THEN
                                             1
                                            ELSE count(parcela)
                                            END
                                      FROM  divida.parcela
                                     WHERE  parcela.num_parcelamento = dpar.num_parcelamento
                                )
                        )::numeric(14,2) AS percentual,

                        CASE WHEN ddi.inscricao_municipal IS NOT NULL THEN
                            (select split_part ( imobiliario.fn_busca_endereco_imovel( ddi.inscricao_municipal ), '§', 3) )
                        ELSE
                            CASE WHEN dde.inscricao_economica IS NOT NULL THEN
                                (   select
                                        split_part (
                                            COALESCE(
                                                economico.fn_busca_domicilio_fiscal( dde.inscricao_economica ),
                                                economico.fn_busca_domicilio_informado( dde.inscricao_economica )
                                            ),
                                            '§',
                                            3
                                        )
                                )
                            ELSE
                                (
                                    SELECT  sw_cgm.logradouro
                                      FROM  sw_cgm
                                     WHERE  sw_cgm.numcgm = ddc.numcgm
                                )
                            END
                        END as nom_logradouro

                    FROM  divida.divida_ativa AS dda

              INNER JOIN  divida.divida_cgm AS ddc
                      ON  ddc.cod_inscricao = dda.cod_inscricao
                     AND  ddc.exercicio = dda.exercicio

               LEFT JOIN  divida.divida_imovel AS ddi
                      ON  ddi.cod_inscricao = ddc.cod_inscricao
                     AND  ddi.exercicio = ddc.exercicio

               LEFT JOIN  divida.divida_empresa AS dde
                      ON  dde.cod_inscricao = ddc.cod_inscricao
                     AND dde.exercicio = ddc.exercicio

              INNER JOIN
                       (
                            SELECT  divida_parcelamento.cod_inscricao,
                                    divida_parcelamento.exercicio,
                                    max(divida_parcelamento.num_parcelamento) AS num_parcelamento
                              FROM  divida.divida_parcelamento ";

                if ($this->getDado('num_parcelamento') != '') {
                    $stSql .= " WHERE divida_parcelamento.num_parcelamento IN (".$this->getDado('num_parcelamento').") ";
                }

                $stSql .= "

                            GROUP BY  divida_parcelamento.cod_inscricao,
                                      divida_parcelamento.exercicio
                        ) AS ddp
                      ON  ddp.cod_inscricao = ddc.cod_inscricao
                          AND ddp.exercicio = ddc.exercicio

              INNER JOIN  divida.parcelamento AS dpar
                      ON  dpar.num_parcelamento = ddp.num_parcelamento ";

        return $stSql;
    }

    public function recuperaConsultaEnvelopeNotificacao(&$rsRecordSet, $stFiltro, $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stSql = $this->montaRecuperaConsultaEnvelopeNotificacao().$stFiltro;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaConsultaEnvelopeNotificacao()
    {
        $stSql   = "SELECT DISTINCT
                        CASE WHEN ddi.inscricao_municipal IS NOT NULL THEN
                            ( select split_part ( imobiliario.fn_busca_endereco_imovel( ddi.inscricao_municipal ), '§', 1)||' '||split_part ( imobiliario.fn_busca_endereco_imovel( ddi.inscricao_municipal ), '§', 3)||', '||split_part ( imobiliario.fn_busca_endereco_imovel( ddi.inscricao_municipal ), '§', 4) )
                        ELSE
                            CASE WHEN dde.inscricao_economica IS NOT NULL THEN
                                (select split_part ( COALESCE( economico.fn_busca_domicilio_fiscal( dde.inscricao_economica ), economico.fn_busca_domicilio_informado( dde.inscricao_economica ) ), '§', 1 ))||' '||(select split_part ( COALESCE( economico.fn_busca_domicilio_fiscal( dde.inscricao_economica ), economico.fn_busca_domicilio_informado( dde.inscricao_economica ) ), '§', 3 ))||', '||(select split_part ( COALESCE( economico.fn_busca_domicilio_fiscal( dde.inscricao_economica ), economico.fn_busca_domicilio_informado( dde.inscricao_economica ) ), '§', 4 ))
                            ELSE
                                (
                                    SELECT
                                        sw_cgm.logradouro ||' '|| sw_cgm.numero ||' '|| sw_cgm.complemento
                                    FROM
                                        sw_cgm
                                    WHERE
                                        sw_cgm.numcgm = ddc.numcgm
                                )
                            END
                        END AS endereco,

                        CASE WHEN ddi.inscricao_municipal IS NOT NULL THEN
                            ( select split_part ( imobiliario.fn_busca_endereco_imovel( ddi.inscricao_municipal ), '§', 7) )
                        ELSE
                            CASE WHEN dde.inscricao_economica IS NOT NULL THEN
                            ( select split_part ( COALESCE( economico.fn_busca_domicilio_fiscal( dde.inscricao_economica ), economico.fn_busca_domicilio_informado( dde.inscricao_economica ) ), '§', 7 ) )
                            ELSE
                                (
                                    SELECT
                                        sw_cgm.cep
                                    FROM
                                        sw_cgm
                                    WHERE
                                        sw_cgm.numcgm = ddc.numcgm
                                )
                            END
                        END AS cep,

                        CASE WHEN ddi.inscricao_municipal IS NOT NULL THEN
                            ( select split_part ( imobiliario.fn_busca_endereco_imovel( ddi.inscricao_municipal ), '§', 6) )
                        ELSE
                            CASE WHEN dde.inscricao_economica IS NOT NULL THEN
                            ( select split_part ( COALESCE( economico.fn_busca_domicilio_fiscal( dde.inscricao_economica ), economico.fn_busca_domicilio_informado( dde.inscricao_economica ) ), '§', 6 ) )
                            ELSE
                                (
                                    SELECT
                                        sw_cgm.bairro
                                    FROM
                                        sw_cgm
                                    WHERE
                                        sw_cgm.numcgm = ddc.numcgm
                                )
                            END
                        END AS bairro,

                        CASE WHEN ddi.inscricao_municipal IS NOT NULL THEN
                            ( select split_part ( imobiliario.fn_busca_endereco_imovel( ddi.inscricao_municipal ), '§', 9) )||'/'||( select split_part ( imobiliario.fn_busca_endereco_imovel( ddi.inscricao_municipal ), '§', 11) )
                        ELSE
                            CASE WHEN dde.inscricao_economica IS NOT NULL THEN
                            (select split_part ( COALESCE( economico.fn_busca_domicilio_fiscal( dde.inscricao_economica ), economico.fn_busca_domicilio_informado( dde.inscricao_economica ) ), '§', 11 ))||'/'||(select split_part ( COALESCE( economico.fn_busca_domicilio_fiscal( dde.inscricao_economica ), economico.fn_busca_domicilio_informado( dde.inscricao_economica ) ), '§', 11 ))
                            ELSE
                                (
                                    SELECT
                                        sw_municipio.nom_municipio || '/' || sw_uf.nom_uf

                                    FROM
                                        sw_cgm

                                    INNER JOIN
                                        sw_uf
                                    ON
                                        sw_uf.cod_pais = sw_cgm.cod_pais
                                        AND sw_uf.cod_uf = sw_cgm.cod_uf

                                    INNER JOIN
                                        sw_municipio
                                    ON
                                        sw_municipio.cod_municipio = sw_cgm.cod_municipio
                                        AND sw_municipio.cod_uf = sw_cgm.cod_uf

                                    WHERE
                                        sw_cgm.numcgm = ddc.numcgm
                                )
                            END
                        END AS cidade_estado,

                        (
                            SELECT
                                sw_cgm.nom_cgm
                            FROM
                                sw_cgm
                            WHERE
                                sw_cgm.numcgm = ddc.numcgm
                        )AS destinatario

                    FROM
                        divida.divida_ativa AS dda

                    INNER JOIN
                        divida.divida_cgm AS ddc
                    ON
                        ddc.cod_inscricao = dda.cod_inscricao
                        AND ddc.exercicio = dda.exercicio

                    LEFT JOIN
                        divida.divida_imovel AS ddi
                    ON
                        ddi.cod_inscricao = dda.cod_inscricao
                        AND ddi.exercicio = dda.exercicio

                    LEFT JOIN
                        divida.divida_empresa AS dde
                    ON
                        dde.cod_inscricao = dda.cod_inscricao
                        AND dde.exercicio = dda.exercicio

                    INNER JOIN
                        divida.divida_parcelamento AS ddp
                    ON
                        ddp.cod_inscricao = dda.cod_inscricao
                        AND ddp.exercicio = dda.exercicio
        \n";

        return $stSql;
    }

   public function recuperaListaCarnesDivida(&$rsRecordSet, $stParametros, $boTransacao = "")
   {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stSql = $this->montaRecuperaListaCarnesDivida().$stParametros;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }
//[convenio_atual]-[carteira_atual]-[impresso]-[chave_vinculo]-[id_vinculo]
    public function montaRecuperaListaCarnesDivida()
    {
        $stSql   = " SELECT DISTINCT
                        (
                            SELECT
                                carne.exercicio
                            FROM
                                arrecadacao.carne
                            WHERE
                                carne.cod_parcela = ap.cod_parcela
                            ORDER BY
                                carne.timestamp DESC
                            LIMIT 1
                        )AS exercicio,
                        COALESCE( ddi.inscricao_municipal, dde.inscricao_economica ) AS inscricao,
                        ddc.numcgm AS numcgm,
                        (
                            SELECT
                                nom_cgm
                            FROM
                                sw_cgm
                            WHERE
                                sw_cgm.numcgm = ddc.numcgm
                        )AS nom_cgm,
                        ddpar.vlr_parcela AS valor_parcela,
                        to_char(ddpar.dt_vencimento_parcela, 'dd/mm/yyyy') AS vencimento_parcela_br,
                        ddpar.dt_vencimento_parcela,
                        ddp.num_parcelamento,
                        ddp.numero_parcelamento,
                        ddpar.num_parcela,
                        (
                            SELECT
                                carne.numeracao
                            FROM
                                arrecadacao.carne
                            WHERE
                                carne.cod_parcela = ap.cod_parcela
                            ORDER BY
                                carne.timestamp DESC
                            LIMIT 1
                        )AS numeracao,
                        (
                            SELECT
                                carne.exercicio
                            FROM
                                arrecadacao.carne
                            WHERE
                                carne.cod_parcela = ap.cod_parcela
                            ORDER BY
                                carne.timestamp DESC
                            LIMIT 1
                        )AS impresso,
                        alc.cod_lancamento,
                        ap.cod_parcela,
                        (
                            SELECT
                                carne.cod_convenio
                            FROM
                                arrecadacao.carne
                            WHERE
                                carne.cod_parcela = ap.cod_parcela
                            ORDER BY
                                carne.timestamp DESC
                            LIMIT 1
                        )AS cod_convenio,
                        (
                            SELECT
                                carne.cod_carteira
                            FROM
                                arrecadacao.carne
                            WHERE
                                carne.cod_parcela = ap.cod_parcela
                            ORDER BY
                                carne.timestamp DESC
                            LIMIT 1
                        )AS cod_carteira,
                        CASE WHEN ddpar.num_parcela = 0 THEN
                            'única'
                        ELSE (
                            SELECT
                                ddpar.num_parcela ||'/'|| count(*) ::text
                            FROM
                                divida.parcela
                            WHERE
                                parcela.num_parcelamento = dp.num_parcelamento
                                AND parcela.num_parcela != 0
                        )
                        END AS info_parcela,
                        arrecadacao.buscaVinculoLancamento ( alc.cod_lancamento, dda.exercicio::integer )::varchar as vinculo,
                        arrecadacao.buscaIdVinculo( alc.cod_lancamento, dda.exercicio::integer )::varchar as id_vinculo,
                        md5(arrecadacao.buscaVinculoLancamento ( alc.cod_lancamento, dda.exercicio::integer ))::varchar as chave_vinculo,
                        ddp.judicial

                    FROM
                        divida.divida_ativa AS dda

                    LEFT JOIN
                        divida.divida_imovel AS ddi
                    ON
                        ddi.cod_inscricao = dda.cod_inscricao
                        AND ddi.exercicio = dda.exercicio

                    LEFT JOIN
                        divida.divida_empresa AS dde
                    ON
                        dde.cod_inscricao = dda.cod_inscricao
                        AND dde.exercicio = dda.exercicio

                    INNER JOIN
                        divida.divida_cgm AS ddc
                    ON
                        ddc.cod_inscricao = dda.cod_inscricao
                        AND ddc.exercicio = dda.exercicio

                    INNER JOIN
                        divida.divida_parcelamento AS dp
                    ON
                        dp.cod_inscricao = dda.cod_inscricao
                        AND dp.exercicio = dda.exercicio

                    INNER JOIN
                        divida.parcelamento  AS ddp
                    ON
                        ddp.num_parcelamento = dp.num_parcelamento

                    INNER JOIN
                        divida.parcela AS ddpar
                    ON
                        ddpar.num_parcelamento = dp.num_parcelamento
                        AND ddpar.cancelada = false
                        AND ddpar.paga = false

                    INNER JOIN
                        divida.parcela_calculo AS dpc
                    ON
                        dpc.num_parcelamento = ddpar.num_parcelamento
                        AND dpc.num_parcela = ddpar.num_parcela

                    INNER JOIN
                        arrecadacao.calculo AS ac
                    ON
                        ac.cod_calculo = dpc.cod_calculo

                    INNER JOIN
                        arrecadacao.lancamento_calculo AS alc
                    ON
                        alc.cod_calculo = ac.cod_calculo

                    INNER JOIN
                        arrecadacao.parcela AS ap
                    ON
                        ap.cod_lancamento = alc.cod_lancamento
                        AND ap.nr_parcela = dpc.num_parcela

                    INNER JOIN
                        arrecadacao.carne AS acne
                    ON
                        acne.cod_parcela = ap.cod_parcela

                    LEFT JOIN
                        divida.divida_cancelada AS ddcanc
                    ON
                        ddcanc.cod_inscricao = dda.cod_inscricao
                        AND ddcanc.exercicio = dda.exercicio

                    LEFT JOIN
                        divida.divida_remissao AS ddrem
                    ON
                        ddrem.cod_inscricao = dda.cod_inscricao
                        AND ddrem.exercicio = dda.exercicio

                    WHERE
                        ddcanc.cod_inscricao IS NULL
                        AND ddrem.cod_inscricao IS NULL \n";

        return $stSql;
    }

    public function recuperaListaRelatorioDivida(&$rsRecordSet, $stParametros, $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stSql = $this->montaRecuperaListaRelatorioDivida().$stParametros;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaListaRelatorioDivida()
    {
        $stSql = "  SELECT DISTINCT
                        ddi.inscricao_municipal
                        , dde.inscricao_economica
                        , dda.cod_inscricao
                        , dda.exercicio
                        , ( COALESCE( ictdf.cod_logradouro, ict.cod_logradouro, edi.cod_logradouro )
                        ) AS cod_logradouro
                        , ddp.num_parcelamento
                        , dp.numero_parcelamento ||'/'|| dp.exercicio AS numero_parcelamento
                        , ddproc.cod_processo ||'/'|| ddproc.ano_exercicio AS processo
                        , arrecadacao.fn_busca_origem_lancamento_sem_exercicio (
                                (
                                    SELECT
                                        ap.cod_lancamento
                                    FROM
                                        divida.parcela_origem AS dpo

                                    INNER JOIN
                                        arrecadacao.parcela AS ap
                                    ON
                                        ap.cod_parcela = dpo.cod_parcela
                                    WHERE
                                        dpo.num_parcelamento = (
                                            SELECT
                                                min(num_parcelamento)
                                            FROM
                                                divida.divida_parcelamento
                                            WHERE
                                                divida.divida_parcelamento.cod_inscricao = dda.cod_inscricao
                                                AND divida.divida_parcelamento.exercicio = dda.exercicio
                                        )
                                    LIMIT 1
                                ),
                                1,
                                1
                        ) AS origem
                        , ddc.numcgm,
                        (
                            SELECT
                                nom_cgm
                            FROM
                                sw_cgm
                            WHERE
                                sw_cgm.numcgm = ddc.numcgm
                        )AS nom_cgm,
                        (
                            CASE WHEN ddi.inscricao_municipal IS NOT NULL THEN
                                imobiliario.fn_busca_endereco_imovel_formatado( ddi.inscricao_municipal )
                            ELSE
                                CASE WHEN dde.inscricao_economica IS NOT NULL THEN
                                    CASE WHEN edf.inscricao_economica IS NOT NULL THEN
                                        split_part ( economico.fn_busca_domicilio_fiscal( dde.inscricao_economica ), '§', 1)||' '||split_part ( economico.fn_busca_domicilio_fiscal( dde.inscricao_economica ), '§', 3)||', '||split_part ( economico.fn_busca_domicilio_fiscal( dde.inscricao_economica ), '§', 4)
                                    ELSE
                                        split_part ( economico.fn_busca_domicilio_informado( dde.inscricao_economica ), '§', 1)||' '||split_part ( economico.fn_busca_domicilio_informado( dde.inscricao_economica ), '§', 3)||', '||split_part ( economico.fn_busca_domicilio_informado( dde.inscricao_economica ), '§', 4)
                                    END
                                ELSE
                                    (
                                        SELECT
                                            sw_cgm.logradouro ||' '|| sw_cgm.numero ||' '|| sw_cgm.complemento
                                        FROM
                                            sw_cgm
                                        WHERE
                                            sw_cgm.numcgm = ddc.numcgm
                                    )
                                END
                            END
                        )AS endereco

                    FROM
                        divida.divida_ativa AS dda

                    INNER JOIN
                        divida.divida_cgm AS ddc
                    ON
                        ddc.cod_inscricao = dda.cod_inscricao
                        AND ddc.exercicio = dda.exercicio

                    LEFT JOIN
                        divida.divida_empresa AS dde
                    ON
                        dde.cod_inscricao = dda.cod_inscricao
                        AND dde.exercicio = dda.exercicio

                    LEFT JOIN
                        economico.domicilio_informado AS edi
                    ON
                        edi.inscricao_economica = dde.inscricao_economica

                    LEFT JOIN
                        economico.domicilio_fiscal AS edf
                    ON
                        edf.inscricao_economica = dde.inscricao_economica

                    LEFT JOIN
                        imobiliario.imovel_confrontacao AS iicdf
                    ON
                        iicdf.inscricao_municipal = edf.inscricao_municipal

                    LEFT JOIN
                        imobiliario.confrontacao_trecho AS ictdf
                    ON
                        ictdf.cod_confrontacao = iicdf.cod_confrontacao
                        AND ictdf.cod_lote = iicdf.cod_lote

                    LEFT JOIN
                        divida.divida_imovel AS ddi
                    ON
                        ddi.cod_inscricao = dda.cod_inscricao
                        AND ddi.exercicio = dda.exercicio

                    LEFT JOIN
                        imobiliario.imovel_confrontacao AS iic
                    ON
                        iic.inscricao_municipal = ddi.inscricao_municipal

                    LEFT JOIN
                        imobiliario.confrontacao_trecho AS ict
                    ON
                        ict.cod_confrontacao = iic.cod_confrontacao
                        AND ict.cod_lote = iic.cod_lote

                    LEFT JOIN
                        divida.divida_processo ddproc
                    ON
                        ddproc.cod_inscricao = dda.cod_inscricao
                        AND ddproc.exercicio = dda.exercicio

                    INNER JOIN (
                        SELECT
                            max(num_parcelamento) as num_parcelamento,
                            cod_inscricao,
                            exercicio
                        FROM
                            divida.divida_parcelamento
                        GROUP BY
                            cod_inscricao, exercicio
                    )AS ddp
                    ON
                        ddp.cod_inscricao = dda.cod_inscricao
                        AND ddp.exercicio = dda.exercicio

                    INNER JOIN
                        divida.parcelamento AS dp
                    ON
                        dp.num_parcelamento = ddp.num_parcelamento  \n";

        return $stSql;
    }

    public function recuperaListaDadosRelatorioDivida(&$rsRecordSet, $stParametros, $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stSql = $this->montaRecuperaListaDadosRelatorioDivida().$stParametros;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaListaDadosRelatorioDivida()
    {
        $stSql = "  SELECT                                                                              \n";
        $stSql .="      dp.vlr_parcela AS valor_total                                                   \n";
        $stSql .="      , dp.num_parcela                                                                \n";
        $stSql .="      , ( SELECT                                                                      \n";
        $stSql .="              sum(dpc.vl_credito)                                                     \n";
        $stSql .="          FROM                                                                        \n";
        $stSql .="              divida.parcela_calculo AS dpc                                           \n";
        $stSql .="          WHERE                                                                       \n";
        $stSql .="              dpc.num_parcelamento = dp.num_parcelamento                              \n";
        $stSql .="              AND dpc.num_parcela = dp.num_parcela                                    \n";
        $stSql .="      ) AS valor_original                                                             \n";
        $stSql .="      , ( SELECT                                                                      \n";
        $stSql .="              sum(*)                                                                  \n";
        $stSql .="          FROM                                                                        \n";
        $stSql .="              divida.parcela                                                          \n";
        $stSql .="          WHERE                                                                       \n";
        $stSql .="              parcela.num_parcelamento = dp.num_parcelamento                          \n";
        $stSql .="      ) AS total_de_parcelas                                                          \n";
        $stSql .="      , ( SELECT                                                                      \n";
        $stSql .="              sum(dpr.valor)                                                          \n";
        $stSql .="          FROM                                                                        \n";
        $stSql .="              divida.parcela_reducao AS dpr                                           \n";
        $stSql .="          WHERE                                                                       \n";
        $stSql .="              dpr.num_parcelamento = dp.num_parcelamento                              \n";
        $stSql .="              AND dpr.num_parcela = dp.num_parcela                                    \n";
        $stSql .="      ) AS valor_reducao                                                              \n";
        $stSql .="      , ( SELECT                                                                      \n";
        $stSql .="              sum(dpa.vlracrescimo)                                                   \n";
        $stSql .="          FROM                                                                        \n";
        $stSql .="              divida.parcela_acrescimo AS dpa                                         \n";
        $stSql .="          WHERE                                                                       \n";
        $stSql .="              dpa.num_parcelamento = dp.num_parcelamento                              \n";
        $stSql .="              AND dpa.num_parcela = dp.num_parcela                                    \n";
        $stSql .="              AND dpa.cod_tipo = 2                                                    \n";
        $stSql .="      ) AS valor_juros                                                                \n";
        $stSql .="      , ( SELECT                                                                      \n";
        $stSql .="              sum(dpa.vlracrescimo)                                                   \n";
        $stSql .="          FROM                                                                        \n";
        $stSql .="              divida.parcela_acrescimo AS dpa                                         \n";
        $stSql .="          WHERE                                                                       \n";
        $stSql .="              dpa.num_parcelamento = dp.num_parcelamento                              \n";
        $stSql .="              AND dpa.num_parcela = dp.num_parcela                                    \n";
        $stSql .="              AND dpa.cod_tipo = 3                                                    \n";
        $stSql .="      ) AS valor_multa                                                                \n";
        $stSql .="      , ( SELECT                                                                      \n";
        $stSql .="              sum(dpa.vlracrescimo)                                                   \n";
        $stSql .="          FROM                                                                        \n";
        $stSql .="              divida.parcela_acrescimo AS dpa                                         \n";
        $stSql .="          WHERE                                                                       \n";
        $stSql .="              dpa.num_parcelamento = dp.num_parcelamento                              \n";
        $stSql .="              AND dpa.num_parcela = dp.num_parcela                                    \n";
        $stSql .="              AND dpa.cod_tipo = 1                                                    \n";
        $stSql .="      ) AS valor_correcao                                                             \n";
        $stSql .="      , ( CASE WHEN dp.paga = true THEN                                               \n";
        $stSql .="              'Paga'                                                                  \n";
        $stSql .="          ELSE                                                                        \n";
        $stSql .="              CASE WHEN dp.cancelada = true THEN                                      \n";
        $stSql .="                  'Cancelada'                                                         \n";
        $stSql .="              ELSE                                                                    \n";
        $stSql .="                  'Aberta'                                                            \n";
        $stSql .="              END                                                                     \n";
        $stSql .="          END                                                                         \n";
        $stSql .="      ) AS situacao                                                                   \n";

        $stSql .="  FROM                                                                                \n";

        $stSql .="      divida.parcela AS dp                                                            \n";

        return $stSql;

    }

    public function recuperaListaDadosRelatorioOrigemDivida(&$rsRecordSet, $stParametros, $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stSql = $this->montaRecuperaListaDadosRelatorioOrigemDivida($stParametros);
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaListaDadosRelatorioOrigemDivida($stParametros)
    {
        $stSql = "  SELECT                                                                                  \n";
        $stSql .="      *                                                                                   \n";
        $stSql .="      , ( valor_original + juros + multa + correcao ) as valor_total                      \n";
        $stSql .="  FROM                                                                                    \n";
        $stSql .="  (                                                                                       \n";
        $stSql .="      SELECT                                                                              \n";
        $stSql .="          *                                                                               \n";
        $stSql .="          , aplica_juro( numeracao, exercicio, cod_parcela, now()::date ) as juros        \n";
        $stSql .="          , aplica_multa( numeracao, exercicio, cod_parcela, now()::date ) as multa       \n";
        $stSql .="          , aplica_correcao( numeracao, exercicio, cod_parcela, now()::date ) as correcao \n";
        $stSql .="      FROM                                                                                \n";
        $stSql .="          (                                                                               \n";
        $stSql .="              SELECT DISTINCT                                                             \n";
        $stSql .="                  dpo.cod_parcela                                                         \n";
        $stSql .="                  , ap.nr_parcela                                                         \n";
        $stSql .="                  , ( ap.nr_parcela||'/'||arrecadacao.fn_total_parcelas(ap.cod_lancamento)\n";
        $stSql .="                  ) as info_parcela                                                       \n";
        $stSql .="                  , ap.valor as valor_original                                            \n";
        $stSql .="                  , max(carne.numeracao) as numeracao                                     \n";
        $stSql .="                  , carne.exercicio::int                                                  \n";
        $stSql .="              FROM                                                                        \n";
        $stSql .="                  divida.parcela_origem as dpo                                            \n";

        $stSql .="                  INNER JOIN arrecadacao.parcela as ap                                    \n";
        $stSql .="                  ON ap.cod_parcela = dpo.cod_parcela                                     \n";

        $stSql .="                  INNER JOIN arrecadacao.carne                                            \n";
        $stSql .="                  ON carne.cod_parcela = ap.cod_parcela                                   \n";

        $stSql .="              ". $stParametros ."                                                         \n";

        $stSql .="              GROUP BY                                                                    \n";
        $stSql .="                  dpo.cod_parcela, ap.nr_parcela, ap.valor, ap.cod_lancamento             \n";
        $stSql .="                  , carne.exercicio                                                       \n";
        $stSql .="          ) as busca                                                                      \n";
        $stSql .="      ORDER BY                                                                            \n";
        $stSql .="          cod_parcela, nr_parcela                                                         \n";
        $stSql .="      ) as somatorio                                                                      \n";

        return $stSql;
    }

    public function recuperaListaDetalhesConsultaDivida(&$rsRecordSet, $stFiltro, $stData, $inNumeracao, $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stSql = $this->montaRecuperaListaDetalhesConsultaDivida( $stFiltro, $stData, $inNumeracao );
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaListaDetalhesConsultaDivida($stFiltro, $stData, $inNumeracao)
    {
        $stSql = "
            SELECT
                split_part ( monetario.fn_busca_mascara_credito( dados.cod_credito, dados.cod_especie, dados.cod_genero, dados.cod_natureza ), '§', 1 ) as credito_codigo_composto,
                split_part ( monetario.fn_busca_mascara_credito( dados.cod_credito, dados.cod_especie, dados.cod_genero, dados.cod_natureza ), '§', 6 ) as credito_nome,
                dados.vl_credito AS valor_credito,
                CASE WHEN '".$stData."' > dados.dt_vencimento_parcela THEN
                    dados.vlr_acrescimos_juros + split_part( aplica_acrescimo_modalidade( dados.cod_modalidade, 2, dados.num_parcelamento ,dados.vlr_parcela, dados.dt_vencimento_parcela, '".$stData."' ), ';', 1 )::numeric
                ELSE
                    dados.vlr_acrescimos_juros
                END AS credito_juros_pagar,

                CASE WHEN '".$stData."' > dados.dt_vencimento_parcela THEN
                    dados.vlr_acrescimos_multa + split_part( aplica_acrescimo_modalidade( dados.cod_modalidade, 3, dados.num_parcelamento ,dados.vlr_parcela, dados.dt_vencimento_parcela, '".$stData."' ), ';', 1 )::numeric
                ELSE
                    dados.vlr_acrescimos_multa
                END AS credito_multa_pagar,

                CASE WHEN '".$stData."' > dados.dt_vencimento_parcela THEN
                    dados.vlr_acrescimos_correcao + split_part( aplica_acrescimo_modalidade( dados.cod_modalidade, 1, dados.num_parcelamento, dados.vlr_parcela, dados.dt_vencimento_parcela, '".$stData."' ), ';', 1 )::numeric
                ELSE
                    dados.vlr_acrescimos_correcao
                END AS credito_correcao_pagar,

                dados.vlr_reducao AS credito_descontos,

                CASE WHEN '".$stData."' > dados.dt_vencimento_parcela THEN
                    split_part( aplica_acrescimo_modalidade( dados.cod_modalidade, 1, dados.num_parcelamento, dados.vlr_parcela, dados.dt_vencimento_parcela, '".$stData."' ), ';', 1 )::numeric
                    + split_part( aplica_acrescimo_modalidade( dados.cod_modalidade, 3, dados.num_parcelamento, dados.vlr_parcela, dados.dt_vencimento_parcela, '".$stData."' ), ';', 1 )::numeric
                    + split_part( aplica_acrescimo_modalidade( dados.cod_modalidade, 2, dados.num_parcelamento, dados.vlr_parcela, dados.dt_vencimento_parcela, '".$stData."' ), ';', 1 )::numeric
                    + dados.vlr_total
                ELSE
                    dados.vlr_total
                END AS valor_total,
                dados.cod_calculo,
                (dados.vlr_pago + dados.vlr_pago_acrescimo) AS valor_total_pago,

                CASE WHEN '".$stData."' > dados.dt_vencimento_parcela THEN
                    (dados.vlr_pago + dados.vlr_pago_acrescimo) - (split_part( aplica_acrescimo_modalidade( dados.cod_modalidade, 1, dados.num_parcelamento, dados.vlr_parcela, dados.dt_vencimento_parcela, '".$stData."' ), ';', 1 )::numeric
                    + split_part( aplica_acrescimo_modalidade( dados.cod_modalidade, 3, dados.num_parcelamento, dados.vlr_parcela, dados.dt_vencimento_parcela, '".$stData."' ), ';', 1 )::numeric
                    + split_part( aplica_acrescimo_modalidade( dados.cod_modalidade, 2, dados.num_parcelamento, dados.vlr_parcela, dados.dt_vencimento_parcela, '".$stData."' ), ';', 1 )::numeric
                    + dados.vlr_total)
                ELSE
                    (dados.vlr_pago + dados.vlr_pago_acrescimo) - dados.vlr_total
                END AS diferenca

            FROM
                (
                SELECT
                    dados.inscricao,
                    dados.cod_modalidade,
                    dp.vlr_parcela,
                    dp.dt_vencimento_parcela,
                    ac.*,
                    dpc.vl_credito,
                    (
                        SELECT
                            sum(vlracrescimo)
                        FROM
                            divida.parcela_acrescimo
                        WHERE
                            parcela_acrescimo.num_parcelamento = dpc.num_parcelamento
                            AND parcela_acrescimo.num_parcela = dpc.num_parcela
                            AND parcela_acrescimo.cod_tipo = 2
                    )AS vlr_acrescimos_juros,
                    (
                        SELECT
                            sum(vlracrescimo)
                        FROM
                            divida.parcela_acrescimo
                        WHERE
                            parcela_acrescimo.num_parcelamento = dpc.num_parcelamento
                            AND parcela_acrescimo.num_parcela = dpc.num_parcela
                            AND parcela_acrescimo.cod_tipo = 3
                    )AS vlr_acrescimos_multa,
                    (
                        SELECT
                            sum(vlracrescimo)
                        FROM
                            divida.parcela_acrescimo
                        WHERE
                            parcela_acrescimo.num_parcelamento = dpc.num_parcelamento
                            AND parcela_acrescimo.num_parcela = dpc.num_parcela
                            AND parcela_acrescimo.cod_tipo = 1
                    )AS vlr_acrescimos_correcao,
                    (
                        SELECT
                            sum(vlracrescimo)
                        FROM
                            divida.parcela_acrescimo
                        WHERE
                            parcela_acrescimo.num_parcelamento = dpc.num_parcelamento
                            AND parcela_acrescimo.num_parcela = dpc.num_parcela
                    )AS vlr_acrescimos,
                    (
                        SELECT
                            sum(valor)
                        FROM
                            divida.parcela_reducao
                        WHERE
                            parcela_reducao.num_parcelamento = dpc.num_parcelamento
                            AND parcela_reducao.num_parcela = dpc.num_parcela
                    )AS vlr_reducao,
                    (
                        SELECT
                            sum(vlracrescimo)+dpc.vl_credito - (
                                                                    SELECT
                                                                        sum(valor)
                                                                    FROM
                                                                        divida.parcela_reducao
                                                                    WHERE
                                                                        parcela_reducao.num_parcelamento = dpc.num_parcelamento
                                                                        AND parcela_reducao.num_parcela = dpc.num_parcela
                                                                )

                        FROM
                            divida.parcela_acrescimo
                        WHERE
                            parcela_acrescimo.num_parcelamento = dpc.num_parcelamento
                            AND parcela_acrescimo.num_parcela = dpc.num_parcela
                    )AS vlr_total,
                    COALESCE( (
                        SELECT
                            sum(pagamento_acrescimo.valor)
                        FROM
                            arrecadacao.pagamento_acrescimo
                        WHERE
                            pagamento_acrescimo.cod_calculo = ac.cod_calculo
                            AND pagamento_acrescimo.numeracao = apc.numeracao
                    ), 0.00 ) AS vlr_pago_acrescimo,
                    apc.valor AS vlr_pago

                FROM
                    arrecadacao.parcela AS ap

                INNER JOIN
                    divida.parcela_calculo AS dpc
                ON
                    ap.nr_parcela = dpc.num_parcela

                INNER JOIN (
                    SELECT DISTINCT
                        coalesce ( ddi.inscricao_municipal, dde.inscricao_economica, ddc.numcgm) AS inscricao,
                        dp.cod_modalidade,
                        dp.num_parcelamento

                    FROM
                        divida.divida_cgm AS ddc

                    LEFT JOIN
                        divida.divida_imovel AS ddi
                    ON
                        ddi.cod_inscricao = ddc.cod_inscricao
                        AND ddi.exercicio = ddc.exercicio

                    LEFT JOIN
                        divida.divida_empresa AS dde
                    ON
                        dde.cod_inscricao = ddc.cod_inscricao
                        AND dde.exercicio = ddc.exercicio

                    INNER JOIN
                        divida.divida_parcelamento AS ddp
                    ON
                        ddp.cod_inscricao = ddc.cod_inscricao
                        AND ddp.exercicio = ddc.exercicio

                    INNER JOIN
                        divida.parcelamento AS dp
                    ON
                        ddp.num_parcelamento = dp.num_parcelamento
                ) AS dados
                ON
                    dados.num_parcelamento =  dpc.num_parcelamento

                INNER JOIN
                    divida.parcela AS dp
                ON
                    dp.num_parcela = dpc.num_parcela
                    AND dp.num_parcelamento = dpc.num_parcelamento

                INNER JOIN
                    arrecadacao.calculo AS ac
                ON
                    ac.cod_calculo = dpc.cod_calculo

                LEFT JOIN
                    arrecadacao.pagamento_calculo AS apc
                ON
                    apc.cod_calculo = dpc.cod_calculo
                    AND apc.numeracao = ".$inNumeracao."::varchar
                WHERE
                    ".$stFiltro."
            )AS dados
        \n";

        return $stSql;
    }

    public function recuperaListaDetalhesAcrescimosConsultaDivida(&$rsRecordSet, $stFiltro, $stData, $inNumeracao, $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stSql = $this->montaRecuperaListaDetalhesAcrescimosConsultaDivida( $stFiltro, $stData, $inNumeracao );
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaListaDetalhesAcrescimosConsultaDivida($stFiltro, $stData, $inNumeracao)
    {
        $stSql = " SELECT DISTINCT
                    dados.cod_modalidade,
                    split_part ( monetario.fn_busca_mascara_credito( dados.cod_credito, dados.cod_especie, dados.cod_genero, dados.cod_natureza ), '§', 1 ) as credito_codigo_composto,
                    split_part ( monetario.fn_busca_mascara_credito( dados.cod_credito, dados.cod_especie, dados.cod_genero, dados.cod_natureza ), '§', 6 ) as credito_nome,
                    dados.vl_credito AS valor_credito,
                    CASE WHEN '".$stData."' > dados.dt_vencimento_parcela THEN
                        dados.vlr_acrescimos_juros + split_part( aplica_acrescimo_modalidade( 0, dados.cod_inscricao, dados.exercicio_divida, dados.cod_modalidade, 2, dados.num_parcelamento ,dados.vlr_parcela, dados.dt_vencimento_parcela, '".$stData."', 'true' ), ';', 1 )::numeric
                    ELSE
                        dados.vlr_acrescimos_juros
                    END AS credito_juros_pagar,

                    CASE WHEN '".$stData."' > dados.dt_vencimento_parcela THEN
                        aplica_acrescimo_modalidade( 0, dados.cod_inscricao, dados.exercicio_divida, dados.cod_modalidade, 2, dados.num_parcelamento ,dados.vlr_parcela, dados.dt_vencimento_parcela, '".$stData."', 'true' )
                    END AS juros_sob_juros_pagar,

                    CASE WHEN '".$stData."' > dados.dt_vencimento_parcela THEN
                        dados.vlr_acrescimos_multa + split_part( aplica_acrescimo_modalidade( 0, dados.cod_inscricao, dados.exercicio_divida, dados.cod_modalidade, 3, dados.num_parcelamento ,dados.vlr_parcela, dados.dt_vencimento_parcela, '".$stData."', 'true' ), ';', 1 )::numeric
                    ELSE
                        dados.vlr_acrescimos_multa
                    END AS credito_multa_pagar,

                    CASE WHEN '".$stData."' > dados.dt_vencimento_parcela THEN
                        aplica_acrescimo_modalidade( 0, dados.cod_inscricao, dados.exercicio_divida, dados.cod_modalidade, 3, dados.num_parcelamento ,dados.vlr_parcela, dados.dt_vencimento_parcela, '".$stData."', 'true' )
                    END AS multa_sob_multa_pagar,

                    CASE WHEN '".$stData."' > dados.dt_vencimento_parcela THEN
                        dados.vlr_acrescimos_correcao + split_part( aplica_acrescimo_modalidade( 0, dados.cod_inscricao, dados.exercicio_divida, dados.cod_modalidade, 1, dados.num_parcelamento, dados.vlr_parcela, dados.dt_vencimento_parcela, '".$stData."', 'true' ), ';', 1 )::numeric
                    ELSE
                        dados.vlr_acrescimos_correcao
                    END AS credito_correcao_pagar,

                    CASE WHEN '".$stData."' > dados.dt_vencimento_parcela THEN
                        aplica_acrescimo_modalidade( 0, dados.cod_inscricao, dados.exercicio_divida, dados.cod_modalidade, 1, dados.num_parcelamento, dados.vlr_parcela, dados.dt_vencimento_parcela, '".$stData."', 'true' )
                    END AS correcao_sob_correcao_pagar,

                    dados.vlr_reducao AS credito_descontos,

                    CASE WHEN '".$stData."' > dados.dt_vencimento_parcela THEN
                        split_part( aplica_acrescimo_modalidade( 0, dados.cod_inscricao, dados.exercicio_divida, dados.cod_modalidade, 1, dados.num_parcelamento, dados.vlr_parcela, dados.dt_vencimento_parcela, '".$stData."', 'true' ), ';', 1 )::numeric
                        + split_part( aplica_acrescimo_modalidade( 0, dados.cod_inscricao, dados.exercicio_divida, dados.cod_modalidade, 3, dados.num_parcelamento, dados.vlr_parcela, dados.dt_vencimento_parcela, '".$stData."', 'true' ), ';', 1 )::numeric
                        + split_part( aplica_acrescimo_modalidade( 0, dados.cod_inscricao, dados.exercicio_divida, dados.cod_modalidade, 2, dados.num_parcelamento, dados.vlr_parcela, dados.dt_vencimento_parcela, '".$stData."', 'true' ), ';', 1 )::numeric
                        + dados.vlr_total
                    ELSE
                        dados.vlr_total
                    END AS valor_total,
                    dados.cod_calculo,
                    (dados.vlr_pago + dados.vlr_pago_acrescimo) AS valor_total_pago,

                    CASE WHEN '".$stData."' > dados.dt_vencimento_parcela THEN
                        (dados.vlr_pago + dados.vlr_pago_acrescimo) - (split_part( aplica_acrescimo_modalidade( 0, dados.cod_inscricao, dados.exercicio_divida, dados.cod_modalidade, 1, dados.num_parcelamento, dados.vlr_parcela, dados.dt_vencimento_parcela, '".$stData."', 'true' ), ';', 1 )::numeric
                        + split_part( aplica_acrescimo_modalidade( 0, dados.cod_inscricao, dados.exercicio_divida, dados.cod_modalidade, 3, dados.num_parcelamento, dados.vlr_parcela, dados.dt_vencimento_parcela, '".$stData."', 'true' ), ';', 1 )::numeric
                        + split_part( aplica_acrescimo_modalidade( 0, dados.cod_inscricao, dados.exercicio_divida, dados.cod_modalidade, 2, dados.num_parcelamento, dados.vlr_parcela, dados.dt_vencimento_parcela, '".$stData."', 'true' ), ';', 1 )::numeric
                        + dados.vlr_total)
                    ELSE
                        (dados.vlr_pago + dados.vlr_pago_acrescimo) - dados.vlr_total
                    END AS diferenca,
                    COALESCE( dados.vlracrescimo, 0.00 ) AS valor_acrescimo_individual,
                    dados.cod_tipo AS tipo_acrescimo_individual,
                    dados.cod_acrescimo AS cod_acrescimo_individual,
                    (
                        SELECT
                            acrescimo.descricao_acrescimo
                        FROM
                            monetario.acrescimo
                        WHERE
                            acrescimo.cod_tipo = dados.cod_tipo
                            AND acrescimo.cod_acrescimo = dados.cod_acrescimo
                    )AS descricao_acrescimo_individual

                FROM
                    (
                    SELECT
                        dados.inscricao,
                        dados.cod_modalidade,
                        dados.num_parcelamento,
                        dp.vlr_parcela,
                        dp.dt_vencimento_parcela,
                        ac.*,
                        dpc.vl_credito,
                        (
                            SELECT
                                sum(vlracrescimo)
                            FROM
                                divida.parcela_acrescimo
                            WHERE
                                parcela_acrescimo.num_parcelamento = dpc.num_parcelamento
                                AND parcela_acrescimo.num_parcela = dpc.num_parcela
                                AND parcela_acrescimo.cod_tipo = 2
                        )AS vlr_acrescimos_juros,
                        (
                            SELECT
                                sum(vlracrescimo)
                            FROM
                                divida.parcela_acrescimo
                            WHERE
                                parcela_acrescimo.num_parcelamento = dpc.num_parcelamento
                                AND parcela_acrescimo.num_parcela = dpc.num_parcela
                                AND parcela_acrescimo.cod_tipo = 3
                        )AS vlr_acrescimos_multa,
                        (
                            SELECT
                                sum(vlracrescimo)
                            FROM
                                divida.parcela_acrescimo
                            WHERE
                                parcela_acrescimo.num_parcelamento = dpc.num_parcelamento
                                AND parcela_acrescimo.num_parcela = dpc.num_parcela
                                AND parcela_acrescimo.cod_tipo = 1
                        )AS vlr_acrescimos_correcao,
                        (
                            SELECT
                                sum(vlracrescimo)
                            FROM
                                divida.parcela_acrescimo
                            WHERE
                                parcela_acrescimo.num_parcelamento = dpc.num_parcelamento
                                AND parcela_acrescimo.num_parcela = dpc.num_parcela
                        )AS vlr_acrescimos,
                        (
                            SELECT
                                sum(valor)
                            FROM
                                divida.parcela_reducao
                            WHERE
                                parcela_reducao.num_parcelamento = dpc.num_parcelamento
                                AND parcela_reducao.num_parcela = dpc.num_parcela
                        )AS vlr_reducao,
                        (
                            SELECT
                                sum(vlracrescimo)+dpc.vl_credito - (
                                    SELECT
                                        sum(valor)
                                    FROM
                                        divida.parcela_reducao
                                    WHERE
                                        parcela_reducao.num_parcelamento = dpc.num_parcelamento
                                        AND parcela_reducao.num_parcela = dpc.num_parcela
                                )

                            FROM
                                divida.parcela_acrescimo
                            WHERE
                                parcela_acrescimo.num_parcelamento = dpc.num_parcelamento
                                AND parcela_acrescimo.num_parcela = dpc.num_parcela
                        )AS vlr_total,
                        COALESCE( (
                            SELECT
                                sum(pagamento_acrescimo.valor)
                            FROM
                                arrecadacao.pagamento_acrescimo
                            WHERE
                                pagamento_acrescimo.cod_calculo = ac.cod_calculo
                                AND pagamento_acrescimo.numeracao = apc.numeracao
                        ), 0.00 ) AS vlr_pago_acrescimo,
                        apc.valor AS vlr_pago,
                        dpac.vlracrescimo,
                        dmac.cod_tipo,
                        dmac.cod_acrescimo,
                        dados.cod_inscricao,
                        dados.exercicio_divida::integer

                    FROM
                        arrecadacao.parcela AS ap

                    INNER JOIN
                        divida.parcela_calculo AS dpc
                    ON
                        ap.nr_parcela = dpc.num_parcela

                    INNER JOIN (
                        SELECT DISTINCT
                            coalesce ( ddi.inscricao_municipal, dde.inscricao_economica, ddc.numcgm) AS inscricao,
                            dp.cod_modalidade,
                            dp.num_parcelamento,
                            (
                                SELECT
                                    modalidade.ultimo_timestamp
                                FROM
                                    divida.modalidade
                                WHERE
                                    modalidade.cod_modalidade = dp.cod_modalidade
                            )AS timestamp_modalidade,
                            ddc.cod_inscricao,
                            ddc.exercicio AS exercicio_divida

                        FROM
                            divida.divida_cgm AS ddc

                        LEFT JOIN
                            divida.divida_imovel AS ddi
                        ON
                            ddi.cod_inscricao = ddc.cod_inscricao
                            AND ddi.exercicio = ddc.exercicio

                        LEFT JOIN
                            divida.divida_empresa AS dde
                        ON
                            dde.cod_inscricao = ddc.cod_inscricao
                            AND dde.exercicio = ddc.exercicio

                        INNER JOIN
                            divida.divida_parcelamento AS ddp
                        ON
                            ddp.cod_inscricao = ddc.cod_inscricao
                            AND ddp.exercicio = ddc.exercicio

                        INNER JOIN
                            divida.parcelamento AS dp
                        ON
                            ddp.num_parcelamento = dp.num_parcelamento
                    ) AS dados
                    ON
                        dados.num_parcelamento =  dpc.num_parcelamento

                    INNER JOIN
                        divida.parcela AS dp
                    ON
                        dp.num_parcela = dpc.num_parcela
                        AND dp.num_parcelamento = dpc.num_parcelamento

                    INNER JOIN
                        divida.modalidade_acrescimo AS dmac
                    ON
                        dmac.cod_modalidade = dados.cod_modalidade
                        AND dmac.timestamp = dados.timestamp_modalidade
                        AND ( dmac.pagamento = false OR ( dmac.pagamento = true AND '".$stData."' > dp.dt_vencimento_parcela ) )

                    LEFT JOIN
                        divida.parcela_acrescimo AS dpac
                    ON
                        dpac.num_parcela = dpc.num_parcela
                        AND dpac.num_parcelamento = dpc.num_parcelamento
                        AND dmac.cod_acrescimo = dpac.cod_acrescimo
                        AND dmac.cod_tipo = dpac.cod_tipo

                    INNER JOIN
                        arrecadacao.calculo AS ac
                    ON
                        ac.cod_calculo = dpc.cod_calculo

                    LEFT JOIN
                        arrecadacao.pagamento_calculo AS apc
                    ON
                        apc.cod_calculo = dpc.cod_calculo
                        AND apc.numeracao = '".$inNumeracao."'::varchar
                    WHERE
                        ".$stFiltro."
                )AS dados \n";

        return $stSql;
    }

    public function RecuperaValoresCapaCarneDividaRefis2009MataSaoJoao(&$rsRecordSet, $stFiltro, $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql = $this->montaRecuperaValoresCapaCarneDividaRefis2009MataSaoJoao().$stFiltro;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaValoresCapaCarneDividaRefis2009MataSaoJoao()
    {
        if (!$this->getDado('judicial')) {
            $this->setDado('judicial', 0);
        }
        $stSql = " SELECT DISTINCT
                        (
                            SELECT
                                sum(dpo.valor)

                            FROM
                                divida.parcela_origem AS dpo

                            WHERE
                                dpo.num_parcelamento = (
                                    SELECT
                                        divida.divida_parcelamento.num_parcelamento
                                    FROM
                                        divida.divida_parcelamento
                                    WHERE
                                        divida.divida_parcelamento.cod_inscricao = dda.cod_inscricao
                                        AND divida.divida_parcelamento.exercicio = dda.exercicio
                                    ORDER BY
                                        divida.divida_parcelamento.num_parcelamento ASC
                                    LIMIT 1
                                )
                                AND dpo.cod_parcela IN (
                                    SELECT
                                        dpo2.cod_parcela
                                    FROM
                                        divida.parcela_origem AS dpo2
                                    WHERE
                                        dpo2.num_parcelamento = ddp.num_parcelamento
                                        AND dpo2.cod_parcela = dpo.cod_parcela
                                )
                        )AS valor_origem_devido,

                        split_part(
                            aplica_acrescimo_modalidade( ".$this->getDado('judicial').", dda.cod_inscricao, dda.exercicio::integer,
                                dpar.cod_modalidade,
                                3,
                                dpar.num_parcelamento,
                                (
                                    SELECT
                                        sum(dpo.valor)
                                    FROM
                                        divida.parcela_origem AS dpo
                                    WHERE
                                        dpo.num_parcelamento = (
                                            SELECT
                                                divida.divida_parcelamento.num_parcelamento
                                            FROM
                                                divida.divida_parcelamento
                                            WHERE
                                                divida.divida_parcelamento.cod_inscricao = dda.cod_inscricao
                                                AND divida.divida_parcelamento.exercicio = dda.exercicio
                                            ORDER BY
                                                divida.divida_parcelamento.num_parcelamento ASC
                                            LIMIT 1
                                        )
                                        AND dpo.cod_parcela IN (
                                            SELECT
                                                dpo2.cod_parcela
                                            FROM
                                                divida.parcela_origem AS dpo2
                                            WHERE
                                                dpo2.num_parcelamento = ddp.num_parcelamento
                                                AND dpo2.cod_parcela = dpo.cod_parcela
                                        )
                                ),
                                dda.dt_vencimento_origem,
                                dp.dt_vencimento_parcela,
                                'false'
                            ),
                         ';',
                            2
                        ) AS multa_mora,

                        split_part(
                            aplica_acrescimo_modalidade( ".$this->getDado('judicial').", dda.cod_inscricao, dda.exercicio::integer,
                                dpar.cod_modalidade,
                                3,
                                dpar.num_parcelamento,
                                (
                                    SELECT
                                        sum(dpo.valor)
                                    FROM
                                        divida.parcela_origem AS dpo
                                    WHERE
                                        dpo.num_parcelamento = (
                                            SELECT
                                                divida.divida_parcelamento.num_parcelamento
                                            FROM
                                                divida.divida_parcelamento
                                            WHERE
                                                divida.divida_parcelamento.cod_inscricao = dda.cod_inscricao
                                                AND divida.divida_parcelamento.exercicio = dda.exercicio
                                            ORDER BY
                                                divida.divida_parcelamento.num_parcelamento ASC
                                            LIMIT 1
                                        )
                                        AND dpo.cod_parcela IN (
                                            SELECT
                                                dpo2.cod_parcela
                                            FROM
                                                divida.parcela_origem AS dpo2
                                            WHERE
                                                dpo2.num_parcelamento = ddp.num_parcelamento
                                                AND dpo2.cod_parcela = dpo.cod_parcela
                                        )
                                ),
                                dda.dt_vencimento_origem,
                                dp.dt_vencimento_parcela,
                                'false'
                            ),
                        ';',
                            5
                        ) AS multa2_mora,

                        split_part(
                            aplica_acrescimo_modalidade( ".$this->getDado('judicial').", dda.cod_inscricao, dda.exercicio::integer,
                                dpar.cod_modalidade,
                                2,
                                dpar.num_parcelamento,
                                (
                                    SELECT
                                        sum(dpo.valor)
                                    FROM
                                        divida.parcela_origem AS dpo
                                    WHERE
                                        dpo.num_parcelamento = (
                                            SELECT
                                                divida.divida_parcelamento.num_parcelamento
                                            FROM
                                                divida.divida_parcelamento
                                            WHERE
                                                divida.divida_parcelamento.cod_inscricao = dda.cod_inscricao
                                                AND divida.divida_parcelamento.exercicio = dda.exercicio
                                            ORDER BY
                                                divida.divida_parcelamento.num_parcelamento ASC
                                            LIMIT 1
                                        )
                                        AND dpo.cod_parcela IN (
                                            SELECT
                                                dpo2.cod_parcela
                                            FROM
                                                divida.parcela_origem AS dpo2
                                            WHERE
                                                dpo2.num_parcelamento = ddp.num_parcelamento
                                                AND dpo2.cod_parcela = dpo.cod_parcela
                                        )
                                ),
                                dda.dt_vencimento_origem,
                                dp.dt_vencimento_parcela,
                                'false'
                            ),
                            ';',
                            2
                        ) AS juros_mora,

                        split_part(
                            aplica_acrescimo_modalidade( ".$this->getDado('judicial').", dda.cod_inscricao, dda.exercicio::integer,
                                dpar.cod_modalidade,
                                2,
                                dpar.num_parcelamento,
                                (
                                    SELECT
                                        sum(dpo.valor)
                                    FROM
                                        divida.parcela_origem AS dpo
                                    WHERE
                                        dpo.num_parcelamento = (
                                            SELECT
                                                divida.divida_parcelamento.num_parcelamento
                                            FROM
                                                divida.divida_parcelamento
                                            WHERE
                                                divida.divida_parcelamento.cod_inscricao = dda.cod_inscricao
                                                AND divida.divida_parcelamento.exercicio = dda.exercicio
                                            ORDER BY
                                                divida.divida_parcelamento.num_parcelamento ASC
                                            LIMIT 1
                                        )
                                        AND dpo.cod_parcela IN (
                                            SELECT
                                                dpo2.cod_parcela
                                            FROM
                                                divida.parcela_origem AS dpo2
                                            WHERE
                                                dpo2.num_parcelamento = ddp.num_parcelamento
                                                AND dpo2.cod_parcela = dpo.cod_parcela
                                        )
                                ),
                                dda.dt_vencimento_origem,
                                dp.dt_vencimento_parcela,
                                'false'
                            ),
                            ';',
                            1
                        ) AS juros2_mora,
                        split_part(
                            aplica_acrescimo_modalidade( ".$this->getDado('judicial').", dda.cod_inscricao, dda.exercicio::integer,
                                dpar.cod_modalidade,
                                1,
                                dpar.num_parcelamento,
                                (
                                    SELECT
                                        sum(dpo.valor)
                                    FROM
                                        divida.parcela_origem AS dpo
                                    WHERE
                                        dpo.num_parcelamento = (
                                            SELECT
                                                divida.divida_parcelamento.num_parcelamento
                                            FROM
                                                divida.divida_parcelamento
                                            WHERE
                                                divida.divida_parcelamento.cod_inscricao = dda.cod_inscricao
                                                AND divida.divida_parcelamento.exercicio = dda.exercicio
                                            ORDER BY
                                                divida.divida_parcelamento.num_parcelamento ASC
                                            LIMIT 1
                                        )
                                        AND dpo.cod_parcela IN (
                                            SELECT
                                                dpo2.cod_parcela
                                            FROM
                                                divida.parcela_origem AS dpo2
                                            WHERE
                                                dpo2.num_parcelamento = ddp.num_parcelamento
                                                AND dpo2.cod_parcela = dpo.cod_parcela
                                        )
                                ),
                                dda.dt_vencimento_origem,
                                dp.dt_vencimento_parcela,
                                'false'
                            ),
                            ';',
                            2
                        ) AS atualizacao_mora,
                        split_part(
                            aplica_acrescimo_modalidade( ".$this->getDado('judicial').", dda.cod_inscricao, dda.exercicio::integer,
                                dpar.cod_modalidade,
                                1,
                                dpar.num_parcelamento,
                                (
                                    SELECT
                                        sum(dpo.valor)
                                    FROM
                                        divida.parcela_origem AS dpo
                                    WHERE
                                        dpo.num_parcelamento = (
                                            SELECT
                                                divida.divida_parcelamento.num_parcelamento
                                            FROM
                                                divida.divida_parcelamento
                                            WHERE
                                                divida.divida_parcelamento.cod_inscricao = dda.cod_inscricao
                                                AND divida.divida_parcelamento.exercicio = dda.exercicio
                                            ORDER BY
                                                divida.divida_parcelamento.num_parcelamento ASC
                                            LIMIT 1
                                        )
                                        AND dpo.cod_parcela IN (
                                            SELECT
                                                dpo2.cod_parcela
                                            FROM
                                                divida.parcela_origem AS dpo2
                                            WHERE
                                                dpo2.num_parcelamento = ddp.num_parcelamento
                                                AND dpo2.cod_parcela = dpo.cod_parcela
                                        )
                                ),
                                dda.dt_vencimento_origem,
                                dp.dt_vencimento_parcela,
                                'false'
                            ),
                            ';',
                            5
                        ) AS atualizacao2_mora,

                        (
                            SELECT
                                COALESCE(
                                    (
                                        SELECT valor from
                                            imobiliario.atributo_lote_urbano_valor as ialu
                                        WHERE ialu.cod_atributo = 7
                                        AND  ialu.cod_lote = il.cod_lote
                                        ORDER BY ialu.timestamp DESC limit 1
                                    ),
                                    (
                                        SELECT valor from
                                            imobiliario.atributo_lote_rural_valor as ialr
                                        WHERE ialr.cod_atributo = 7
                                        AND  ialr.cod_lote = il.cod_lote
                                        ORDER BY ialr.timestamp DESC limit 1
                                    )
                                )::varchar AS valor
                            FROM
                                imobiliario.lote as il

                            WHERE il.cod_lote = (
                                SELECT
                                    iic.cod_lote
                                FROM
                                    imobiliario.imovel_confrontacao AS iic
                                WHERE
                                    iic.inscricao_municipal = ddi.inscricao_municipal
                            )
                        ) AS numero_lote,
                        (
                            SELECT
                                COALESCE(
                                    (
                                        SELECT valor from
                                            imobiliario.atributo_lote_urbano_valor as ialu
                                        WHERE ialu.cod_atributo = 5
                                        AND  ialu.cod_lote = il.cod_lote
                                        ORDER BY ialu.timestamp DESC limit 1
                                    ),
                                    (
                                        SELECT valor from
                                            imobiliario.atributo_lote_rural_valor as ialr
                                        WHERE ialr.cod_atributo = 5
                                        AND  ialr.cod_lote = il.cod_lote
                                        ORDER BY ialr.timestamp DESC limit 1
                                    )
                                )::varchar AS valor
                            FROM
                                imobiliario.lote as il

                            WHERE il.cod_lote = (
                                SELECT
                                    iic.cod_lote
                                FROM
                                    imobiliario.imovel_confrontacao AS iic
                                WHERE
                                    iic.inscricao_municipal = ddi.inscricao_municipal
                            )
                        ) AS numero_quadra,
                        (
                            SELECT
                                tmp_il.nom_localizacao
                            FROM
                                imobiliario.localizacao AS tmp_il

                            INNER JOIN
                                imobiliario.localizacao_nivel AS tmp_iln
                            ON
                                tmp_il.codigo_composto = tmp_iln.valor || '.00'
                                AND tmp_iln.cod_nivel = 1
                                AND tmp_iln.cod_localizacao = (
                                    SELECT
                                        il.cod_localizacao
                                    FROM
                                        imobiliario.imovel_confrontacao AS iic

                                    INNER JOIN
                                        imobiliario.lote_localizacao AS ill
                                    ON
                                        ill.cod_lote = iic.cod_lote

                                    INNER JOIN
                                        imobiliario.localizacao AS il
                                    ON
                                        il.cod_localizacao = ill.cod_localizacao

                                    WHERE
                                    iic.inscricao_municipal = ddi.inscricao_municipal
                                )
                        ) AS regiao,
                        (
                            SELECT
                                il.nom_localizacao
                            FROM
                                imobiliario.imovel_confrontacao AS iic

                            INNER JOIN
                                imobiliario.lote_localizacao AS ill
                            ON
                                ill.cod_lote = iic.cod_lote

                            INNER JOIN
                                imobiliario.localizacao AS il
                            ON
                                il.cod_localizacao = ill.cod_localizacao

                            WHERE
                            iic.inscricao_municipal = ddi.inscricao_municipal
                        ) AS distrito,

                        (
                            SELECT
                                sum(valor)
                            FROM
                                divida.parcela_reducao
                            WHERE
                                divida.parcela_reducao.num_parcelamento = ddp.num_parcelamento
                        )AS total_reducao,

                        arrecadacao.fn_busca_origem_lancamento_divida_ativa ( dda.cod_inscricao, dda.exercicio::integer, 1 ) AS imposto_taxa,

                        CASE WHEN ddi.inscricao_municipal IS NOT NULL THEN
                            ( select split_part ( imobiliario.fn_busca_endereco_imovel( ddi.inscricao_municipal ), '§', 7) )
                        ELSE
                            CASE WHEN dde.inscricao_economica IS NOT NULL THEN
                                (   select
                                        split_part (
                                            COALESCE(
                                                economico.fn_busca_domicilio_fiscal( dde.inscricao_economica ),
                                                economico.fn_busca_domicilio_informado( dde.inscricao_economica )
                                            ),
                                            '§',
                                            7
                                        )
                                )
                            ELSE
                                (
                                    SELECT
                                        cep
                                    FROM
                                        sw_cgm
                                    WHERE
                                        sw_cgm.numcgm = ddc.numcgm
                                )
                            END
                        END AS cep,

                        CASE WHEN ddi.inscricao_municipal IS NOT NULL THEN
                            (SELECT
                                ic.nom_condominio
                            FROM
                                imobiliario.imovel_condominio AS iic

                            INNER JOIN
                                imobiliario.condominio AS ic
                            ON
                                ic.cod_condominio = iic.cod_condominio

                            WHERE
                                iic.inscricao_municipal = ddi.inscricao_municipal
                            ORDER BY ic.timestamp DESC LIMIT 1
                            )
                        ELSE
                            CASE WHEN dde.inscricao_economica IS NOT NULL THEN
                                (SELECT
                                    CASE WHEN (edf.timestamp > edi.timestamp) OR (edi.timestamp IS NULL AND edf.timestamp IS NOT NULL) THEN
                                        (
                                            SELECT
                                                ic.nom_condominio
                                            FROM
                                                imobiliario.imovel_condominio AS iic

                                            INNER JOIN
                                                imobiliario.condominio AS ic
                                            ON
                                                ic.cod_condominio = iic.cod_condominio

                                            WHERE
                                                iic.inscricao_municipal = edf.inscricao_municipal
                                            ORDER BY ic.timestamp DESC LIMIT 1
                                        )
                                    END
                                FROM
                                    economico.cadastro_economico AS ece

                                LEFT JOIN
                                    (
                                        SELECT
                                            max( edf.timestamp ) as timestamp,
                                            edf.inscricao_economica,
                                            edf.inscricao_municipal

                                        FROM
                                            economico.domicilio_fiscal AS edf

                                        GROUP BY
                                            edf.inscricao_economica,
                                            edf.inscricao_municipal
                                    )AS edf
                                ON
                                    edf.inscricao_economica = ece.inscricao_economica

                                LEFT JOIN
                                    (
                                        SELECT
                                            edi.inscricao_economica,
                                            max( edi.timestamp ) AS timestamp
                                        FROM
                                            economico.domicilio_informado AS edi

                                        GROUP BY
                                            edi.inscricao_economica
                                    )AS edi
                                ON
                                    edi.inscricao_economica = ece.inscricao_economica

                                WHERE
                                    ece.inscricao_economica = dde.inscricao_economica
                                )
                            END
                        END AS condominio,

                        CASE WHEN ddi.inscricao_municipal IS NOT NULL THEN
                            ( select split_part ( imobiliario.fn_busca_endereco_imovel( ddi.inscricao_municipal ), '§', 5) )
                        ELSE
                            CASE WHEN dde.inscricao_economica IS NOT NULL THEN
                                (   select
                                        split_part (
                                            COALESCE(
                                                economico.fn_busca_domicilio_fiscal( dde.inscricao_economica ),
                                                economico.fn_busca_domicilio_informado( dde.inscricao_economica )
                                            ),
                                            '§',
                                            5
                                        )
                                )
                            ELSE
                                (
                                    SELECT
                                        complemento
                                    FROM
                                        sw_cgm
                                    WHERE
                                        sw_cgm.numcgm = ddc.numcgm
                                )
                            END
                        END AS complemento,

                        (
                            SELECT
                                nom_cgm
                            FROM
                                sw_cgm
                            WHERE
                                sw_cgm.numcgm = ddc.numcgm
                        )AS nom_cgm,

                        CASE WHEN ddi.inscricao_municipal IS NOT NULL THEN
                            ( select split_part ( imobiliario.fn_busca_endereco_imovel( ddi.inscricao_municipal ), '§', 2) )
                        ELSE
                            CASE WHEN dde.inscricao_economica IS NOT NULL THEN
                                (   select
                                        split_part (
                                            COALESCE(
                                                economico.fn_busca_domicilio_fiscal( dde.inscricao_economica ),
                                                economico.fn_busca_domicilio_informado( dde.inscricao_economica )
                                            ),
                                            '§',
                                            2
                                        )
                                )
                            ELSE
                                (
                                    '0' --pro cgm soh quando usar o esquema novo do caca
                                )
                            END
                        END AS cod_logradouro,

                        CASE WHEN ddi.inscricao_municipal IS NOT NULL THEN
                            ( select split_part ( imobiliario.fn_busca_endereco_imovel( ddi.inscricao_municipal ), '§', 1)||' '||split_part ( imobiliario.fn_busca_endereco_imovel( ddi.inscricao_municipal ), '§', 3)||', '||split_part ( imobiliario.fn_busca_endereco_imovel( ddi.inscricao_municipal ), '§', 4) )
                        ELSE
                            CASE WHEN dde.inscricao_economica IS NOT NULL THEN
                                (select split_part ( COALESCE( economico.fn_busca_domicilio_fiscal( dde.inscricao_economica ), economico.fn_busca_domicilio_informado( dde.inscricao_economica ) ), '§', 1 ))||' '||(select split_part ( COALESCE( economico.fn_busca_domicilio_fiscal( dde.inscricao_economica ), economico.fn_busca_domicilio_informado( dde.inscricao_economica ) ), '§', 3 ))||', '||(select split_part ( COALESCE( economico.fn_busca_domicilio_fiscal( dde.inscricao_economica ), economico.fn_busca_domicilio_informado( dde.inscricao_economica ) ), '§', 4 ))
                            ELSE
                                (
                                    SELECT
                                        sw_cgm.logradouro ||' '|| sw_cgm.numero ||' '|| sw_cgm.complemento
                                    FROM
                                        sw_cgm
                                    WHERE
                                        sw_cgm.numcgm = ddc.numcgm
                                )
                            END
                        END AS endereco,
                    dpar.timestamp AS timestamper,
                    dpar.num_parcelamento

                    FROM
                        divida.divida_ativa AS dda

                    INNER JOIN
                        divida.divida_cgm AS ddc
                    ON
                        ddc.cod_inscricao = dda.cod_inscricao
                        AND ddc.exercicio = dda.exercicio

                    LEFT JOIN
                        divida.divida_imovel AS ddi
                    ON
                        ddi.cod_inscricao = dda.cod_inscricao
                        AND ddi.exercicio = dda.exercicio

                    LEFT JOIN
                        divida.divida_empresa AS dde
                    ON
                        dde.cod_inscricao = dda.cod_inscricao
                        AND dde.exercicio = dda.exercicio

                    INNER JOIN
                        (
                            SELECT
                                divida_parcelamento.cod_inscricao,
                                divida_parcelamento.exercicio,
                                max(divida_parcelamento.num_parcelamento) AS num_parcelamento
                            FROM
                                divida.divida_parcelamento
                            GROUP BY
                                divida_parcelamento.cod_inscricao,
                                divida_parcelamento.exercicio
                        )AS ddp
                    ON
                        ddp.cod_inscricao = dda.cod_inscricao
                        AND ddp.exercicio = dda.exercicio

                    INNER JOIN
                        divida.parcelamento AS dpar
                    ON
                        dpar.num_parcelamento = ddp.num_parcelamento

                    INNER JOIN
                        divida.parcela AS dp
                    ON
                        dp.num_parcelamento = dpar.num_parcelamento
                        AND dp.num_parcela = 1

                    WHERE \n";

        return $stSql;
    }

    public function RecuperaValoresCapaCarneDividaMataSaoJoao(&$rsRecordSet, $stFiltro, $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql = $this->montaRecuperaValoresCapaCarneDividaMataSaoJoao().$stFiltro;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaValoresCapaCarneDividaMataSaoJoao()
    {
        $stSql = " SELECT DISTINCT
                        (
                            SELECT
                                sum(dpo.valor)

                            FROM
                                divida.parcela_origem AS dpo

                            WHERE
                                dpo.num_parcelamento = (
                                    SELECT
                                        divida.divida_parcelamento.num_parcelamento
                                    FROM
                                        divida.divida_parcelamento
                                    WHERE
                                        divida.divida_parcelamento.cod_inscricao = dda.cod_inscricao
                                        AND divida.divida_parcelamento.exercicio = dda.exercicio
                                    ORDER BY
                                        divida.divida_parcelamento.num_parcelamento ASC
                                    LIMIT 1
                                )
                        )AS valor_origem_devido,

                        split_part(
                            aplica_acrescimo_modalidade( 0, dda.cod_inscricao, dda.exercicio::integer,
                                dpar.cod_modalidade,
                                3,
                                dpar.num_parcelamento,
                                (
                                    SELECT
                                        sum(dpo.valor)
                                    FROM
                                        divida.parcela_origem AS dpo
                                    WHERE
                                        dpo.num_parcelamento = (
                                            SELECT
                                                divida.divida_parcelamento.num_parcelamento
                                            FROM
                                                divida.divida_parcelamento
                                            WHERE
                                                divida.divida_parcelamento.cod_inscricao = dda.cod_inscricao
                                                AND divida.divida_parcelamento.exercicio = dda.exercicio
                                            ORDER BY
                                                divida.divida_parcelamento.num_parcelamento ASC
                                            LIMIT 1
                                        )
                                ),
                                dda.dt_vencimento_origem,
                                dp.dt_vencimento_parcela,
                                'false'
                            ),
                         ';',
                            2
                        ) AS multa_mora,

                        split_part(
                            aplica_acrescimo_modalidade( 0, dda.cod_inscricao, dda.exercicio::integer,
                                dpar.cod_modalidade,
                                3,
                                dpar.num_parcelamento,
                                (
                                    SELECT
                                        sum(dpo.valor)
                                    FROM
                                        divida.parcela_origem AS dpo
                                    WHERE
                                        dpo.num_parcelamento = (
                                            SELECT
                                                divida.divida_parcelamento.num_parcelamento
                                            FROM
                                                divida.divida_parcelamento
                                            WHERE
                                                divida.divida_parcelamento.cod_inscricao = dda.cod_inscricao
                                                AND divida.divida_parcelamento.exercicio = dda.exercicio
                                            ORDER BY
                                                divida.divida_parcelamento.num_parcelamento ASC
                                            LIMIT 1
                                        )
                                ),
                                dda.dt_vencimento_origem,
                                dp.dt_vencimento_parcela,
                                'false'
                            ),
                        ';',
                            5
                        ) AS multa2_mora,

                        split_part(
                            aplica_acrescimo_modalidade( 0, dda.cod_inscricao, dda.exercicio::integer,
                                dpar.cod_modalidade,
                                2,
                                dpar.num_parcelamento,
                                (
                                    SELECT
                                        sum(dpo.valor)
                                    FROM
                                        divida.parcela_origem AS dpo
                                    WHERE
                                        dpo.num_parcelamento = (
                                            SELECT
                                                divida.divida_parcelamento.num_parcelamento
                                            FROM
                                                divida.divida_parcelamento
                                            WHERE
                                                divida.divida_parcelamento.cod_inscricao = dda.cod_inscricao
                                                AND divida.divida_parcelamento.exercicio = dda.exercicio
                                            ORDER BY
                                                divida.divida_parcelamento.num_parcelamento ASC
                                            LIMIT 1
                                        )
                                ),
                                dda.dt_vencimento_origem,
                                dp.dt_vencimento_parcela,
                                'false'
                            ),
                            ';',
                            2
                        ) AS juros_mora,

                        split_part(
                            aplica_acrescimo_modalidade( 0, dda.cod_inscricao, dda.exercicio::integer,
                                dpar.cod_modalidade,
                                2,
                                dpar.num_parcelamento,
                                (
                                    SELECT
                                        sum(dpo.valor)
                                    FROM
                                        divida.parcela_origem AS dpo
                                    WHERE
                                        dpo.num_parcelamento = (
                                            SELECT
                                                divida.divida_parcelamento.num_parcelamento
                                            FROM
                                                divida.divida_parcelamento
                                            WHERE
                                                divida.divida_parcelamento.cod_inscricao = dda.cod_inscricao
                                                AND divida.divida_parcelamento.exercicio = dda.exercicio
                                            ORDER BY
                                                divida.divida_parcelamento.num_parcelamento ASC
                                            LIMIT 1
                                        )
                                ),
                                dda.dt_vencimento_origem,
                                dp.dt_vencimento_parcela,
                                'false'
                            ),
                            ';',
                            5
                        ) AS juros2_mora,
                        split_part(
                            aplica_acrescimo_modalidade( 0, dda.cod_inscricao, dda.exercicio::integer,
                                dpar.cod_modalidade,
                                1,
                                dpar.num_parcelamento,
                                (
                                    SELECT
                                        sum(dpo.valor)
                                    FROM
                                        divida.parcela_origem AS dpo
                                    WHERE
                                        dpo.num_parcelamento = (
                                            SELECT
                                                divida.divida_parcelamento.num_parcelamento
                                            FROM
                                                divida.divida_parcelamento
                                            WHERE
                                                divida.divida_parcelamento.cod_inscricao = dda.cod_inscricao
                                                AND divida.divida_parcelamento.exercicio = dda.exercicio
                                            ORDER BY
                                                divida.divida_parcelamento.num_parcelamento ASC
                                            LIMIT 1
                                        )
                                ),
                                dda.dt_vencimento_origem,
                                dp.dt_vencimento_parcela,
                                'false'
                            ),
                            ';',
                            2
                        ) AS atualizacao_mora,
                        split_part(
                            aplica_acrescimo_modalidade( 0, dda.cod_inscricao, dda.exercicio::integer,
                                dpar.cod_modalidade,
                                1,
                                dpar.num_parcelamento,
                                (
                                    SELECT
                                        sum(dpo.valor)
                                    FROM
                                        divida.parcela_origem AS dpo
                                    WHERE
                                        dpo.num_parcelamento = (
                                            SELECT
                                                divida.divida_parcelamento.num_parcelamento
                                            FROM
                                                divida.divida_parcelamento
                                            WHERE
                                                divida.divida_parcelamento.cod_inscricao = dda.cod_inscricao
                                                AND divida.divida_parcelamento.exercicio = dda.exercicio
                                            ORDER BY
                                                divida.divida_parcelamento.num_parcelamento ASC
                                            LIMIT 1
                                        )
                                ),
                                dda.dt_vencimento_origem,
                                dp.dt_vencimento_parcela,
                                'false'
                            ),
                            ';',
                            5
                        ) AS atualizacao2_mora,

                        (
                            SELECT
                                COALESCE(
                                    (
                                        SELECT valor from
                                            imobiliario.atributo_lote_urbano_valor as ialu
                                        WHERE ialu.cod_atributo = 7
                                        AND  ialu.cod_lote = il.cod_lote
                                        ORDER BY ialu.timestamp DESC limit 1
                                    ),
                                    (
                                        SELECT valor from
                                            imobiliario.atributo_lote_rural_valor as ialr
                                        WHERE ialr.cod_atributo = 7
                                        AND  ialr.cod_lote = il.cod_lote
                                        ORDER BY ialr.timestamp DESC limit 1
                                    )
                                )::varchar AS valor
                            FROM
                                imobiliario.lote as il

                            WHERE il.cod_lote = (
                                SELECT
                                    iic.cod_lote
                                FROM
                                    imobiliario.imovel_confrontacao AS iic
                                WHERE
                                    iic.inscricao_municipal = ddi.inscricao_municipal
                            )
                        ) AS numero_lote,
                        (
                            SELECT
                                COALESCE(
                                    (
                                        SELECT valor from
                                            imobiliario.atributo_lote_urbano_valor as ialu
                                        WHERE ialu.cod_atributo = 5
                                        AND  ialu.cod_lote = il.cod_lote
                                        ORDER BY ialu.timestamp DESC limit 1
                                    ),
                                    (
                                        SELECT valor from
                                            imobiliario.atributo_lote_rural_valor as ialr
                                        WHERE ialr.cod_atributo = 5
                                        AND  ialr.cod_lote = il.cod_lote
                                        ORDER BY ialr.timestamp DESC limit 1
                                    )
                                )::varchar AS valor
                            FROM
                                imobiliario.lote as il

                            WHERE il.cod_lote = (
                                SELECT
                                    iic.cod_lote
                                FROM
                                    imobiliario.imovel_confrontacao AS iic
                                WHERE
                                    iic.inscricao_municipal = ddi.inscricao_municipal
                            )
                        ) AS numero_quadra,
                        (
                            SELECT
                                tmp_il.nom_localizacao
                            FROM
                                imobiliario.localizacao AS tmp_il

                            INNER JOIN
                                imobiliario.localizacao_nivel AS tmp_iln
                            ON
                                tmp_il.codigo_composto = tmp_iln.valor || '.00'
                                AND tmp_iln.cod_nivel = 1
                                AND tmp_iln.cod_localizacao = (
                                    SELECT
                                        il.cod_localizacao
                                    FROM
                                        imobiliario.imovel_confrontacao AS iic

                                    INNER JOIN
                                        imobiliario.lote_localizacao AS ill
                                    ON
                                        ill.cod_lote = iic.cod_lote

                                    INNER JOIN
                                        imobiliario.localizacao AS il
                                    ON
                                        il.cod_localizacao = ill.cod_localizacao

                                    WHERE
                                    iic.inscricao_municipal = ddi.inscricao_municipal
                                )
                        ) AS regiao,
                        (
                            SELECT
                                il.nom_localizacao
                            FROM
                                imobiliario.imovel_confrontacao AS iic

                            INNER JOIN
                                imobiliario.lote_localizacao AS ill
                            ON
                                ill.cod_lote = iic.cod_lote

                            INNER JOIN
                                imobiliario.localizacao AS il
                            ON
                                il.cod_localizacao = ill.cod_localizacao

                            WHERE
                            iic.inscricao_municipal = ddi.inscricao_municipal
                        ) AS distrito,

                        (
                            SELECT
                                sum(valor)
                            FROM
                                divida.parcela_reducao
                            WHERE
                                divida.parcela_reducao.num_parcelamento = ddp.num_parcelamento
                        )AS total_reducao,

                        arrecadacao.fn_busca_origem_lancamento_divida_ativa ( dda.cod_inscricao, dda.exercicio::integer, 1 ) AS imposto_taxa,

                        CASE WHEN ddi.inscricao_municipal IS NOT NULL THEN
                            ( select split_part ( imobiliario.fn_busca_endereco_imovel( ddi.inscricao_municipal ), '§', 7) )
                        ELSE
                            CASE WHEN dde.inscricao_economica IS NOT NULL THEN
                                (   select
                                        split_part (
                                            COALESCE(
                                                economico.fn_busca_domicilio_fiscal( dde.inscricao_economica ),
                                                economico.fn_busca_domicilio_informado( dde.inscricao_economica )
                                            ),
                                            '§',
                                            7
                                        )
                                )
                            ELSE
                                (
                                    SELECT
                                        cep
                                    FROM
                                        sw_cgm
                                    WHERE
                                        sw_cgm.numcgm = ddc.numcgm
                                )
                            END
                        END AS cep,

                        CASE WHEN ddi.inscricao_municipal IS NOT NULL THEN
                            (SELECT
                                ic.nom_condominio
                            FROM
                                imobiliario.imovel_condominio AS iic

                            INNER JOIN
                                imobiliario.condominio AS ic
                            ON
                                ic.cod_condominio = iic.cod_condominio

                            WHERE
                                iic.inscricao_municipal = ddi.inscricao_municipal
                            ORDER BY ic.timestamp DESC LIMIT 1
                            )
                        ELSE
                            CASE WHEN dde.inscricao_economica IS NOT NULL THEN
                                (SELECT
                                    CASE WHEN (edf.timestamp > edi.timestamp) OR (edi.timestamp IS NULL AND edf.timestamp IS NOT NULL) THEN
                                        (
                                            SELECT
                                                ic.nom_condominio
                                            FROM
                                                imobiliario.imovel_condominio AS iic

                                            INNER JOIN
                                                imobiliario.condominio AS ic
                                            ON
                                                ic.cod_condominio = iic.cod_condominio

                                            WHERE
                                                iic.inscricao_municipal = edf.inscricao_municipal
                                            ORDER BY ic.timestamp DESC LIMIT 1
                                        )
                                    END
                                FROM
                                    economico.cadastro_economico AS ece

                                LEFT JOIN
                                    (
                                        SELECT
                                            max( edf.timestamp ) as timestamp,
                                            edf.inscricao_economica,
                                            edf.inscricao_municipal

                                        FROM
                                            economico.domicilio_fiscal AS edf

                                        GROUP BY
                                            edf.inscricao_economica,
                                            edf.inscricao_municipal
                                    )AS edf
                                ON
                                    edf.inscricao_economica = ece.inscricao_economica

                                LEFT JOIN
                                    (
                                        SELECT
                                            edi.inscricao_economica,
                                            max( edi.timestamp ) AS timestamp
                                        FROM
                                            economico.domicilio_informado AS edi

                                        GROUP BY
                                            edi.inscricao_economica
                                    )AS edi
                                ON
                                    edi.inscricao_economica = ece.inscricao_economica

                                WHERE
                                    ece.inscricao_economica = dde.inscricao_economica
                                )
                            END
                        END AS condominio,

                        CASE WHEN ddi.inscricao_municipal IS NOT NULL THEN
                            ( select split_part ( imobiliario.fn_busca_endereco_imovel( ddi.inscricao_municipal ), '§', 5) )
                        ELSE
                            CASE WHEN dde.inscricao_economica IS NOT NULL THEN
                                (   select
                                        split_part (
                                            COALESCE(
                                                economico.fn_busca_domicilio_fiscal( dde.inscricao_economica ),
                                                economico.fn_busca_domicilio_informado( dde.inscricao_economica )
                                            ),
                                            '§',
                                            5
                                        )
                                )
                            ELSE
                                (
                                    SELECT
                                        complemento
                                    FROM
                                        sw_cgm
                                    WHERE
                                        sw_cgm.numcgm = ddc.numcgm
                                )
                            END
                        END AS complemento,

                        (
                            SELECT
                                nom_cgm
                            FROM
                                sw_cgm
                            WHERE
                                sw_cgm.numcgm = ddc.numcgm
                        )AS nom_cgm,

                        CASE WHEN ddi.inscricao_municipal IS NOT NULL THEN
                            ( select split_part ( imobiliario.fn_busca_endereco_imovel( ddi.inscricao_municipal ), '§', 2) )
                        ELSE
                            CASE WHEN dde.inscricao_economica IS NOT NULL THEN
                                (   select
                                        split_part (
                                            COALESCE(
                                                economico.fn_busca_domicilio_fiscal( dde.inscricao_economica ),
                                                economico.fn_busca_domicilio_informado( dde.inscricao_economica )
                                            ),
                                            '§',
                                            2
                                        )
                                )
                            ELSE
                                (
                                    '0' --pro cgm soh quando usar o esquema novo do caca
                                )
                            END
                        END AS cod_logradouro,

                        CASE WHEN ddi.inscricao_municipal IS NOT NULL THEN
                            ( select split_part ( imobiliario.fn_busca_endereco_imovel( ddi.inscricao_municipal ), '§', 1)||' '||split_part ( imobiliario.fn_busca_endereco_imovel( ddi.inscricao_municipal ), '§', 3)||', '||split_part ( imobiliario.fn_busca_endereco_imovel( ddi.inscricao_municipal ), '§', 4) )
                        ELSE
                            CASE WHEN dde.inscricao_economica IS NOT NULL THEN
                                (select split_part ( COALESCE( economico.fn_busca_domicilio_fiscal( dde.inscricao_economica ), economico.fn_busca_domicilio_informado( dde.inscricao_economica ) ), '§', 1 ))||' '||(select split_part ( COALESCE( economico.fn_busca_domicilio_fiscal( dde.inscricao_economica ), economico.fn_busca_domicilio_informado( dde.inscricao_economica ) ), '§', 3 ))||', '||(select split_part ( COALESCE( economico.fn_busca_domicilio_fiscal( dde.inscricao_economica ), economico.fn_busca_domicilio_informado( dde.inscricao_economica ) ), '§', 4 ))
                            ELSE
                                (
                                    SELECT
                                        sw_cgm.logradouro ||' '|| sw_cgm.numero ||' '|| sw_cgm.complemento
                                    FROM
                                        sw_cgm
                                    WHERE
                                        sw_cgm.numcgm = ddc.numcgm
                                )
                            END
                        END AS endereco,
                    dpar.timestamp AS timestamper,
                    dpar.num_parcelamento

                    FROM
                        divida.divida_ativa AS dda

                    INNER JOIN
                        divida.divida_cgm AS ddc
                    ON
                        ddc.cod_inscricao = dda.cod_inscricao
                        AND ddc.exercicio = dda.exercicio

                    LEFT JOIN
                        divida.divida_imovel AS ddi
                    ON
                        ddi.cod_inscricao = ddc.cod_inscricao
                        AND ddi.exercicio = ddc.exercicio

                    LEFT JOIN
                        divida.divida_empresa AS dde
                    ON
                        dde.cod_inscricao = ddc.cod_inscricao
                        AND dde.exercicio = ddc.exercicio

                    INNER JOIN
                        (
                            SELECT
                                divida_parcelamento.cod_inscricao,
                                divida_parcelamento.exercicio,
                                max(divida_parcelamento.num_parcelamento) AS num_parcelamento
                            FROM
                                divida.divida_parcelamento
                            GROUP BY
                                divida_parcelamento.cod_inscricao,
                                divida_parcelamento.exercicio
                        )AS ddp
                    ON
                        ddp.cod_inscricao = ddc.cod_inscricao
                        AND ddp.exercicio = ddc.exercicio

                    INNER JOIN
                        divida.parcelamento AS dpar
                    ON
                        dpar.num_parcelamento = ddp.num_parcelamento

                    INNER JOIN
                        divida.parcela AS dp
                    ON
                        dp.num_parcelamento = ddp.num_parcelamento
                        AND dp.num_parcela = 1

                    WHERE \n";

        return $stSql;
    }

    public function RecuperaCapaCarneDividaMataSaoJoao(&$rsRecordSet, $stFiltro, $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stSql = $this->montaRecuperaCapaCarneDividaMataSaoJoao().$stFiltro;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaCapaCarneDividaMataSaoJoao()
    {
        $stSql = " SELECT DISTINCT
                    ddc.numcgm,
                    dp.num_parcela,
                    ddc.cod_inscricao,
                    ddc.exercicio AS exercicio_da,
                    dpar.numero_parcelamento,
                    dpar.exercicio AS exercicio_cobranca,
                    to_char(dp.dt_vencimento_parcela, 'dd/mm/yyyy') AS dt_vencimento_parcela,
                    to_char(dpar.timestamp, 'dd/mm/yyyy') AS dt_acordo,
                    dp.vlr_parcela,
                    ddi.inscricao_municipal,
                    dde.inscricao_economica,
                    dda.exercicio_original,
                    ddp.num_parcelamento,
                    al.observacao

                FROM
                    divida.divida_ativa AS dda

                INNER JOIN
                    divida.divida_cgm AS ddc
                ON
                    ddc.cod_inscricao = dda.cod_inscricao
                    AND ddc.exercicio = dda.exercicio

                LEFT JOIN
                    divida.divida_imovel AS ddi
                ON
                    ddi.cod_inscricao = ddc.cod_inscricao
                    AND ddi.exercicio = ddc.exercicio

                LEFT JOIN
                    divida.divida_empresa AS dde
                ON
                    dde.cod_inscricao = ddc.cod_inscricao
                    AND dde.exercicio = ddc.exercicio

                INNER JOIN
                    divida.divida_parcelamento AS ddp
                ON
                    ddp.cod_inscricao = ddc.cod_inscricao
                    AND ddp.exercicio = ddc.exercicio

                INNER JOIN
                    divida.parcelamento AS dpar
                ON
                    dpar.num_parcelamento = ddp.num_parcelamento

                INNER JOIN
                    divida.parcela AS dp
                ON
                    dp.num_parcelamento = ddp.num_parcelamento

                INNER JOIN
                    divida.parcela_calculo AS dpc
                ON
                    dpc.num_parcelamento = dp.num_parcelamento
                    AND dpc.num_parcela = dp.num_parcela

                INNER JOIN
                    arrecadacao.lancamento_calculo AS alc
                ON
                    alc.cod_calculo = dpc.cod_calculo

                INNER JOIN
                    arrecadacao.lancamento AS al
                ON
                    al.cod_lancamento = alc.cod_lancamento

                INNER JOIN
                    arrecadacao.parcela AS ap
                ON
                    ap.cod_lancamento = alc.cod_lancamento

                WHERE ";

        return $stSql;
    }

    public function RecuperaEnderecoInscricao(&$rsRecordSet, $stFiltro, $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stSql = $this->montaRecuperaEnderecoInscricao().$stFiltro;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaEnderecoInscricao()
    {
        $stSql = "SELECT
                    arrecadacao.fn_consulta_endereco_todos( coalesce( dde.inscricao_economica, ddi.inscricao_municipal, ddc.numcgm ),
                        CASE WHEN dde.inscricao_economica IS NOT NULL THEN
                            2
                        ELSE
                            CASE WHEN ddi.inscricao_municipal IS NOT NULL THEN
                                1
                            ELSE
                                3
                            END
                        END,
                        1
                    )||'  Bairro: '|| arrecadacao.fn_consulta_endereco_todos( coalesce( dde.inscricao_economica, ddi.inscricao_municipal, ddc.numcgm ),
                        CASE WHEN dde.inscricao_economica IS NOT NULL THEN
                            2
                        ELSE
                            CASE WHEN ddi.inscricao_municipal IS NOT NULL THEN
                                1
                            ELSE
                                3
                            END
                        END,
                        2
                    )||'  CEP: '|| arrecadacao.fn_consulta_endereco_todos( coalesce( dde.inscricao_economica, ddi.inscricao_municipal, ddc.numcgm ),
                        CASE WHEN dde.inscricao_economica IS NOT NULL THEN
                            2
                        ELSE
                            CASE WHEN ddi.inscricao_municipal IS NOT NULL THEN
                                1
                            ELSE
                                3
                            END
                        END,
                        3
                    )AS nom_logradouro,
                    coalesce( dde.inscricao_economica, ddi.inscricao_municipal, ddc.numcgm ) as inscricao

                FROM
                    divida.divida_cgm AS ddc

                INNER JOIN
                    sw_cgm as cgm
                ON
                    cgm.numcgm = ddc.numcgm

                LEFT JOIN
                    divida.divida_imovel AS ddi
                ON
                    ddi.cod_inscricao = ddc.cod_inscricao
                    AND ddi.exercicio = ddc.exercicio

                LEFT JOIN
                    divida.divida_empresa AS dde
                ON
                    dde.cod_inscricao = ddc.cod_inscricao
                    AND dde.exercicio = ddc.exercicio

                LEFT JOIN (
                    SELECT
                        edf_tmp.inscricao_economica,
                        edf_tmp.inscricao_municipal,
                        edf_tmp.timestamp
                    FROM
                        economico.domicilio_fiscal AS edf_tmp,
                        (
                            SELECT
                                MAX (timestamp) AS timestamp,
                                inscricao_economica
                            FROM
                                economico.domicilio_fiscal
                            GROUP BY
                                inscricao_economica
                        )AS tmp
                    WHERE
                        tmp.timestamp = edf_tmp.timestamp
                        AND tmp.inscricao_economica = edf_tmp.inscricao_economica
                )AS edf
                ON
                    edf.inscricao_economica = dde.inscricao_economica

                LEFT JOIN (
                    SELECT
                        edi_tmp.timestamp,
                        edi_tmp.inscricao_economica
                    FROM
                        economico.domicilio_informado AS edi_tmp,
                        (
                            SELECT
                                MAX(timestamp) AS timestamp,
                                inscricao_economica
                            FROM
                                economico.domicilio_informado
                            GROUP BY
                                inscricao_economica
                        )AS tmp
                    WHERE
                        tmp.timestamp = edi_tmp.timestamp
                        AND tmp.inscricao_economica = edi_tmp.inscricao_economica
                )AS edi
                ON
                    dde.inscricao_economica = edi.inscricao_economica ";

        return $stSql;
    }

    public function RecuperaDadosCancelamentoDivida(&$rsRecordSet, $stNumParcelamento, $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stSql = $this->montaRecuperaDadosCancelamentoDivida($stNumParcelamento);
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaDadosCancelamentoDivida($stNumParcelamento)
    {
        $stSql = " SELECT DISTINCT
                        divida_cgm.cod_inscricao,
                        divida_cgm.exercicio,
                        divida_cgm.numcgm,
                        (
                            SELECT
                                arrecadacao.fn_busca_origem_lancamento_sem_exercicio( parcela.cod_lancamento, 1, 1 )
                            FROM
                                arrecadacao.parcela
                            WHERE
                                parcela.cod_parcela = parcela_origem.cod_parcela
                        )AS origem,
                        (
                            SELECT
                                count(*)
                            FROM
                                divida.parcela_origem
                            WHERE
                                parcela_origem.num_parcelamento = divida_parcelamento.num_parcelamento
                        )as qtd_parcelas,
                        (
                            SELECT
                                sum(parcela_origem.valor)
                            FROM
                                divida.parcela_origem
                            WHERE
                                parcela_origem.num_parcelamento = divida_parcelamento.num_parcelamento
                        )as valor_origem,
                        (
                            SELECT
                                nom_cgm
                            FROM
                                sw_cgm
                            WHERE
                                sw_cgm.numcgm = divida_cgm.numcgm
                        )as nom_cgm,

                        (
                            SELECT
                                cpf
                            FROM
                                sw_cgm_pessoa_fisica
                            WHERE
                                sw_cgm_pessoa_fisica.numcgm = divida_cgm.numcgm
                        )as cpf,

                        (
                            SELECT
                                cnpj
                            FROM
                                sw_cgm_pessoa_juridica
                            WHERE
                                sw_cgm_pessoa_juridica.numcgm = divida_cgm.numcgm
                        )as cnpj,

                        divida_imovel.inscricao_municipal,
                        divida_empresa.inscricao_economica,
                        divida_processo.cod_processo||' / '||divida_processo.exercicio AS processo,
                        (
                            SELECT
                                valor
                            FROM
                                administracao.configuracao
                            WHERE
                                cod_modulo = 2
                                AND exercicio = '2007'
                                AND parametro = 'nom_prefeitura'
                        )AS nompref,
                        (
                            SELECT
                                nom_municipio
                            FROM
                                sw_municipio
                            WHERE
                                sw_municipio.cod_municipio = (
                                    SELECT
                                        valor
                                    from
                                        administracao.configuracao
                                    where
                                        cod_modulo = 2
                                        and exercicio = '2007'
                                        and parametro = 'cod_municipio'
                                )::integer
                                AND
                                sw_municipio.cod_uf = (
                                    SELECT
                                        valor
                                    from
                                        administracao.configuracao
                                    where
                                        cod_modulo = 2
                                        and exercicio = '2007'
                                        and parametro = 'cod_uf'
                                )::integer
                        )AS nome_municipio,
                        to_char(now(), 'dd/mm/yyyy' ) AS data_emissao,
                        arrecadacao.fn_consulta_endereco_todos( divida_cgm.numcgm, 3, 1) AS endereco,
                        arrecadacao.fn_consulta_endereco_todos( divida_cgm.numcgm, 3, 2) AS bairro,
                        arrecadacao.fn_consulta_endereco_todos( divida_cgm.numcgm, 3, 3) AS endcep,
                        arrecadacao.fn_consulta_endereco_todos( divida_cgm.numcgm, 3, 4) AS municipio

                    FROM
                        divida.divida_parcelamento

                    LEFT JOIN
                        divida.parcela_origem
                    ON
                        parcela_origem.num_parcelamento = divida_parcelamento.num_parcelamento

                    INNER JOIN
                        divida.divida_cgm
                    ON
                        divida_cgm.cod_inscricao = divida_parcelamento.cod_inscricao
                        AND divida_cgm.exercicio = divida_parcelamento.exercicio

                    LEFT JOIN
                        divida.divida_imovel
                    ON
                        divida_imovel.cod_inscricao = divida_parcelamento.cod_inscricao
                        AND divida_imovel.exercicio = divida_parcelamento.exercicio

                    LEFT JOIN
                        divida.divida_empresa
                    ON
                        divida_empresa.cod_inscricao = divida_parcelamento.cod_inscricao
                        AND divida_empresa.exercicio = divida_parcelamento.exercicio

                    LEFT JOIN
                        divida.divida_processo
                    ON
                        divida_processo.cod_inscricao = divida_parcelamento.cod_inscricao
                        AND divida_processo.exercicio = divida_parcelamento.exercicio

                    WHERE
                        divida_parcelamento.num_parcelamento = ".$stNumParcelamento;

        return $stSql;
    }

    public function RecuperaMatriculaCGM(&$rsRecordSet, $stCGM, $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stSql = $this->montaRecuperaMatriculaCGM($stCGM);
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaMatriculaCGM($stCGM)
    {
        $stSql = " SELECT
                        pc.registro

                    FROM
                        pessoal.servidor AS ps

                    INNER JOIN
                        pessoal.servidor_contrato_servidor AS pscs
                    ON
                        pscs.cod_servidor = ps.cod_servidor

                    INNER JOIN
                        pessoal.contrato AS pc
                    ON
                        pc.cod_contrato = pscs.cod_contrato
                    WHERE
                        ps.numcgm = ".$stCGM;

        return $stSql;
    }

    public function recuperaConsultaTermoConfissao(&$rsRecordSet, $inNumParcelamento, $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stSql = $this->montaRecuperaConsultaTermoConfissao($inNumParcelamento);
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaConsultaTermoConfissao($inNumParcelamento)
    {
        $stSql = " SELECT
                        CASE WHEN cod_acrescimo = 7 THEN
                            '0,50'
                        ELSE
                            '0,75'
                        END AS juros,
                        CASE WHEN cod_acrescimo = 7 THEN
                            'zero ponto cinquenta'
                        ELSE
                            'zero ponto setenta e cinco'
                        END AS juros_escrito

                    FROM
                        divida.parcelamento AS dp

                    INNER JOIN
                        divida.modalidade AS dm
                    ON
                        dm.cod_modalidade = dp.cod_modalidade

                    INNER JOIN
                        divida.modalidade_acrescimo AS dma
                    ON
                        dma.cod_modalidade = dm.cod_modalidade
                        AND dma.timestamp = dm.ultimo_timestamp
                        AND dma.cod_acrescimo in ( 7, 8 )
                        AND dma.cod_tipo = 2

                    WHERE
                        dp.num_parcelamento = ".$inNumParcelamento;

        return $stSql;
    }

    public function recuperaConsultaTermoInscricaoDividaGenericoValorOrigem(&$rsRecordSet, $stFiltro, $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stSql = $this->montaRecuperaConsultaTermoInscricaoDividaGenericoValorOrigem().$stFiltro;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaConsultaTermoInscricaoDividaGenericoValorOrigem()
    {
        $stSql = "   SELECT DISTINCT
                        to_char(now(), 'dd/mm/yyyy') AS dt_notificacao,
                        dda.dt_vencimento_origem,
                        sw_cgm_pessoa_fisica.rg,
                        sw_cgm_pessoa_fisica.orgao_emissor,
                        COALESCE( sw_cgm_pessoa_fisica.cpf, sw_cgm_pessoa_juridica.cnpj ) AS cpf_cnpj,
                        dda.cod_inscricao,
                        dda.exercicio,
                        dpar.cod_modalidade,
                        ddi.inscricao_municipal,
                        dde.inscricao_economica,
                        COALESCE( ddi.inscricao_municipal, dde.inscricao_economica, ddc.numcgm) AS inscricao,
                        dda.num_livro,
                        dda.num_folha,
                        CASE WHEN ddi.inscricao_municipal IS NOT NULL THEN
                            arrecadacao.fn_consulta_endereco_todos( ddi.inscricao_municipal, 1, 1 )
                        ELSE
                            CASE WHEN dde.inscricao_economica IS NOT NULL THEN
                                arrecadacao.fn_consulta_endereco_todos( dde.inscricao_economica, 2, 1 )
                            ELSE
                                arrecadacao.fn_consulta_endereco_todos( ddc.numcgm, 3, 1 )
                            END
                        END AS endereco,

                        CASE WHEN ddi.inscricao_municipal IS NOT NULL THEN
                            arrecadacao.fn_consulta_endereco_todos( ddi.inscricao_municipal, 1, 2 )
                        ELSE
                            CASE WHEN dde.inscricao_economica IS NOT NULL THEN
                                arrecadacao.fn_consulta_endereco_todos( dde.inscricao_economica, 2, 2 )
                            ELSE
                                arrecadacao.fn_consulta_endereco_todos( ddc.numcgm, 3, 2 )
                            END
                        END AS bairro,

                        CASE WHEN ddi.inscricao_municipal IS NOT NULL THEN
                            arrecadacao.fn_consulta_endereco_todos( ddi.inscricao_municipal, 1, 3 )
                        ELSE
                            CASE WHEN dde.inscricao_economica IS NOT NULL THEN
                                arrecadacao.fn_consulta_endereco_todos( dde.inscricao_economica, 2, 3 )
                            ELSE
                                arrecadacao.fn_consulta_endereco_todos( ddc.numcgm, 3, 3 )
                            END
                        END AS cep,
                        parcela_origem.valor AS valor_origem,
                        parcela_origem.cod_credito||'.'||parcela_origem.cod_especie||'.'||parcela_origem.cod_genero||'.'||parcela_origem.cod_natureza||' - '||credito.descricao_credito AS credito_origem,
                        credito.descricao_credito,
                        TO_CHAR(dda.dt_vencimento_origem,'dd/mm/yyyy') as dt_vencimento_br,
                        dda.cod_inscricao||'/'||dda.exercicio as descricao_inscricao_da,
                        
                        parcela_origem.cod_natureza,
                        aplica_acrescimo_modalidade (
                            0,
                            dda.cod_inscricao,
                            dda.exercicio::integer,
                            dpar.cod_modalidade,
                            3,
                            dpar.num_parcelamento,
                            parcela_origem.valor,
                            dda.dt_vencimento_origem,
                            now()::date,
                            'false'
                        ) AS acrescimos_m,

                        aplica_acrescimo_modalidade (
                            0,
                            dda.cod_inscricao,
                            dda.exercicio::integer,
                            dpar.cod_modalidade,
                            2,
                            dpar.num_parcelamento,
                            parcela_origem.valor,
                            dda.dt_vencimento_origem,
                            now()::date,
                            'false'
                        ) AS acrescimos_j,

                        aplica_acrescimo_modalidade (
                            0,
                            dda.cod_inscricao,
                            dda.exercicio::integer,
                            dpar.cod_modalidade,
                            1,
                            dpar.num_parcelamento,
                            parcela_origem.valor,
                            dda.dt_vencimento_origem,
                            now()::date,
                            'false'
                        ) AS acrescimos_c,

                        (
                            SELECT
                                sum(valor)
                            FROM
                                divida.parcela_reducao
                            WHERE
                                divida.parcela_reducao.num_parcelamento = ddp.num_parcelamento
                        )AS total_reducao,
                        dda.exercicio_original AS exercicio_origem,
                        (
                            SELECT
                                (
                                    SELECT
                                        arrecadacao.fn_busca_origem_lancamento ( ap.cod_lancamento, dda.exercicio_original::integer, 1, 1 )
                                    FROM
                                        arrecadacao.parcela AS ap
                                    WHERE
                                        ap.cod_parcela = dpo.cod_parcela
                                )
                            FROM
                                divida.parcela_origem AS dpo
                            WHERE
                                dpo.num_parcelamento = (
                                    SELECT
                                        divida.divida_parcelamento.num_parcelamento
                                    FROM
                                        divida.divida_parcelamento
                                    WHERE
                                        divida.divida_parcelamento.cod_inscricao = dda.cod_inscricao
                                        AND divida.divida_parcelamento.exercicio = dda.exercicio
                                    ORDER BY
                                        divida.divida_parcelamento.num_parcelamento ASC
                                    LIMIT 1
                                )
                                AND dpo.cod_parcela IN (
                                    SELECT
                                        dpo2.cod_parcela
                                    FROM
                                        divida.parcela_origem AS dpo2
                                    WHERE
                                        dpo2.num_parcelamento = ddp.num_parcelamento
                                        AND dpo2.cod_parcela = dpo.cod_parcela
                                )
                                LIMIT 1
                        )AS imposto,

                        sw_cgm.nom_cgm AS contribuinte,
                        sw_cgm.numcgm,

                        dpar.num_parcelamento,
                        to_char( dda.dt_inscricao, 'dd/mm/yyyy' ) AS dt_inscricao_divida

                    FROM
                        divida.divida_ativa AS dda

                    INNER JOIN
                        divida.divida_cgm AS ddc
                    ON
                        ddc.cod_inscricao = dda.cod_inscricao
                        AND ddc.exercicio = dda.exercicio


                    INNER JOIN
                        sw_cgm
                    ON
                        sw_cgm.numcgm = ddc.numcgm

                    LEFT JOIN
                        sw_cgm_pessoa_fisica
                    ON
                        sw_cgm_pessoa_fisica.numcgm = sw_cgm.numcgm

                    LEFT JOIN
                        sw_cgm_pessoa_juridica
                    ON
                        sw_cgm_pessoa_juridica.numcgm = sw_cgm.numcgm

                    LEFT JOIN
                        divida.divida_imovel AS ddi
                    ON
                        ddi.cod_inscricao = ddc.cod_inscricao
                        AND ddi.exercicio = ddc.exercicio

                    LEFT JOIN
                        divida.divida_empresa AS dde
                    ON
                        dde.cod_inscricao = ddc.cod_inscricao
                        AND dde.exercicio = ddc.exercicio

                    INNER JOIN
                        (
                            SELECT
                                divida_parcelamento.cod_inscricao,
                                divida_parcelamento.exercicio,
                                max(divida_parcelamento.num_parcelamento) AS num_parcelamento
                            FROM
                                divida.divida_parcelamento
                            GROUP BY
                                divida_parcelamento.cod_inscricao,
                                divida_parcelamento.exercicio
                        )AS ddp
                    ON
                        ddp.cod_inscricao = ddc.cod_inscricao
                        AND ddp.exercicio = ddc.exercicio

                    INNER JOIN
                        divida.parcelamento AS dpar
                    ON
                        dpar.num_parcelamento = ddp.num_parcelamento

                    INNER JOIN
                        (
                            SELECT
                                min( divida.divida_parcelamento.num_parcelamento ) AS num_parcelamento,
                                divida_parcelamento.cod_inscricao,
                                divida_parcelamento.exercicio
                            FROM
                                divida.divida_parcelamento
                            GROUP BY
                                divida_parcelamento.cod_inscricao,
                                divida_parcelamento.exercicio
                        )AS parcelamento_inscricao
                    ON
                        parcelamento_inscricao.cod_inscricao = ddc.cod_inscricao
                        AND parcelamento_inscricao.exercicio = ddc.exercicio

                    INNER JOIN
                        (
                            SELECT
                                sum( dpo.valor ) as valor,
                                dpo.cod_especie,
                                dpo.cod_genero,
                                dpo.cod_natureza,
                                dpo.cod_credito,
                                dpo.num_parcelamento,
                                dpo2.num_parcelamento AS num_parcelamento_atual
                            FROM
                                divida.parcela_origem AS dpo

                            INNER JOIN
                                divida.parcela_origem AS dpo2
                            ON
                                dpo2.cod_parcela = dpo.cod_parcela

                            GROUP BY
                                dpo.cod_especie,
                                dpo.cod_genero,
                                dpo.cod_natureza,
                                dpo.cod_credito,
                                dpo.num_parcelamento,
                                dpo2.num_parcelamento
                        )AS parcela_origem
                    ON
                        parcela_origem.num_parcelamento_atual = ddp.num_parcelamento
                        AND parcela_origem.num_parcelamento = parcelamento_inscricao.num_parcelamento

                    INNER JOIN
                        monetario.credito
                    ON
                        credito.cod_credito = parcela_origem.cod_credito
                        AND credito.cod_especie = parcela_origem.cod_especie
                        AND credito.cod_genero = parcela_origem.cod_genero
                        AND credito.cod_natureza = parcela_origem.cod_natureza ";

        return $stSql;
    }

    public function recuperaConsultaTermoInscricaoDividaGenericoConfiguracaoUsuario(&$rsRecordSet, $inDocumento, $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        /*
            $inDocumento
                case 1: //"Certidão de Dívida Ativa"
                case 2: //"Termo de Inscricao de Dívida Ativa"
                case 3: //"Memorial de Cálculo da Dívida Ativa"
                case 4: //"Termo Consolidação"
                case 5: //"Termo de Parcelamento"
        */
        $stSql = $this->montaRecuperaConsultaTermoInscricaoDividaGenericoConfiguracaoUsuario($inDocumento);
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaConsultaTermoInscricaoDividaGenericoConfiguracaoUsuario($inDocumento)
    {
      $stSql = " SELECT
            (
                SELECT
                    '" . URBEM_ROOT_URL. "'||'/gestaoAdministrativa/fontes/PHP/framework/temas/padrao/imagens/'||valor
                FROM
                    administracao.configuracao
                WHERE
                    cod_modulo = 2
                    AND exercicio = extract(year from now())::varchar
                    AND parametro = 'logotipo'
            )AS url_logo,
            (

                SELECT
                sw_municipio.nom_municipio

                FROM
                    sw_municipio

                WHERE
                    sw_municipio.cod_municipio IN (
                        SELECT
                            valor::integer
                        FROM
                            administracao.configuracao

                        WHERE
                            cod_modulo = 2
                            AND exercicio = extract(year from now())::varchar
                            AND parametro = 'cod_municipio'
                    ) AND sw_municipio.cod_uf IN (
                        SELECT
                            valor::integer
                        FROM
                            administracao.configuracao

                        WHERE
                            cod_modulo = 2
                            AND exercicio = extract(year from now())::varchar
                            AND parametro = 'cod_uf'
                    )
            ) AS nom_municipio,
            (
                SELECT
                    valor
                FROM
                    administracao.configuracao
                WHERE
                    cod_modulo = 2
                    AND exercicio = extract(year from now())::varchar
                    AND parametro = 'nom_prefeitura'
            ) AS nom_prefeitura,
            (
                SELECT
                    valor
                FROM
                    administracao.configuracao
                WHERE
                    cod_modulo = 33
                    AND exercicio = extract(year from now())::varchar
                    AND parametro = 'secretaria_".$inDocumento."'
            )AS nom_secretaria,

            (
                SELECT
                    valor
                FROM
                    administracao.configuracao
                WHERE
                    cod_modulo = 33
                    AND exercicio = extract(year from now())::varchar
                    AND parametro = 'chefe_departamento_".$inDocumento."'
            )AS nom_chefe_departamento,

            (
                SELECT
                    valor
                FROM
                    administracao.configuracao
                WHERE
                    cod_modulo = 33
                    AND exercicio = extract(year from now())::varchar
                    AND parametro = 'coordenador_".$inDocumento."'
            )AS nom_coordenador,

            (
                SELECT
                    valor
                FROM
                    administracao.configuracao
                WHERE
                    cod_modulo = 33
                    AND exercicio = extract(year from now())::varchar
                    AND parametro = 'setor_arrecadacao_".$inDocumento."'
            )AS setor_arrecadacao,
            (
                SELECT
                    valor
                FROM
                    administracao.configuracao
                WHERE
                    cod_modulo = 33
                    AND exercicio = extract(year from now())::varchar
                    AND parametro = 'metodologia_calculo_".$inDocumento."'
            )AS metodologia_calculo,
            (
                SELECT
                    valor
                FROM
                    administracao.configuracao
                WHERE
                    cod_modulo = 33
                    AND exercicio = extract(year from now())::varchar
                    AND parametro = 'nro_lei_inscricao_da_".$inDocumento."'
            )AS nro_lei_inscricao_da,
            (
                SELECT
                    valor
                FROM
                    administracao.configuracao
                WHERE
                    cod_modulo = 33
                    AND exercicio = extract(year from now())::varchar
                    AND parametro = 'msg_doc_".$inDocumento."'
            )AS msg_doc,
            (
                SELECT
                    valor
                FROM
                    administracao.configuracao
                WHERE
                    cod_modulo = 33
                    AND exercicio = extract(year from now())::varchar
                    AND parametro = 'utilmsg_doc_".$inDocumento."'
            )AS util_msg_doc,
            (
                SELECT
                    valor
                FROM
                    administracao.configuracao
                WHERE
                    cod_modulo = 33
                    AND exercicio = extract(year from now())::varchar
                    AND parametro = 'utilresp2_doc_".$inDocumento."'
            )AS util_resp2_doc,
            (
                SELECT
                    valor
                FROM
                    administracao.configuracao
                WHERE
                    cod_modulo = 33
                    AND exercicio = extract(year from now())::varchar
                    AND parametro = 'utilleida_doc_".$inDocumento."'
            )AS util_leida_doc,
            (
                SELECT
                    valor
                FROM
                    administracao.configuracao
                WHERE
                    cod_modulo = 33
                    AND exercicio = extract(year from now())::varchar
                    AND parametro = 'utilincval_doc_".$inDocumento."'
            )AS util_incval_doc,
            (
                SELECT
                    valor
                FROM
                    administracao.configuracao
                WHERE
                    cod_modulo = 33
                    AND exercicio = extract(year from now())::varchar
                    AND parametro = 'utilmetcalc_doc_".$inDocumento."'
            )AS util_metcalc_doc
        ";

        return $stSql;
    }

    public function recuperaConsultaTermoInscricaoDividaGenericoAcrescimoFundamentacao(&$rsRecordSet, $stFiltro, $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stSql = $this->montaRecuperaConsultaTermoInscricaoDividaGenericoAcrescimoFundamentacao().$stFiltro;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaConsultaTermoInscricaoDividaGenericoAcrescimoFundamentacao()
    {
        $stSql = "
            SELECT
                norma.cod_norma||' - '||norma.nom_norma||' - '||norma.descricao AS norma,
                norma.descricao,
                acrescimo_norma.cod_acrescimo||'.'||acrescimo_norma.cod_tipo||' - '||acrescimo.descricao_acrescimo AS acrescimo

            FROM
                divida.parcelamento

            INNER JOIN
                divida.modalidade_acrescimo
            ON
                modalidade_acrescimo.cod_modalidade = parcelamento.cod_modalidade
                AND modalidade_acrescimo.timestamp = parcelamento.timestamp_modalidade

            INNER JOIN
                monetario.acrescimo
            ON
                acrescimo.cod_acrescimo = modalidade_acrescimo.cod_acrescimo
                AND acrescimo.cod_tipo = modalidade_acrescimo.cod_tipo

            INNER JOIN
                (
                    SELECT
                        acrescimo_norma.*

                    FROM
                        monetario.acrescimo_norma

                    INNER JOIN
                        (
                            SELECT
                                max(timestamp) AS timestamp,
                                cod_acrescimo,
                                cod_tipo
                            FROM
                                monetario.acrescimo_norma
                            GROUP BY
                                cod_acrescimo,
                                cod_tipo
                        )AS tmp
                    ON
                        tmp.cod_acrescimo = acrescimo_norma.cod_acrescimo
                        AND tmp.cod_tipo = acrescimo_norma.cod_tipo
                        AND tmp.timestamp = acrescimo_norma.timestamp
                )AS acrescimo_norma
            ON
                acrescimo_norma.cod_acrescimo = modalidade_acrescimo.cod_acrescimo
                AND acrescimo_norma.cod_tipo = modalidade_acrescimo.cod_tipo

            INNER JOIN
                normas.norma
            ON
                norma.cod_norma = acrescimo_norma.cod_norma

            WHERE
                modalidade_acrescimo.pagamento = false ";

        return $stSql;
    }

    public function recuperaConsultaMemoriaCalculoGenerico(&$rsRecordSet, $inCodInscricao, $inExercicio, $inCodModalidade, $inInscricao, $flValorOrigem, $stDtOrigem, $dtAtual, $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stSql = $this->montaRecuperaConsultaMemoriaCalculoGenerico( $inCodInscricao, $inExercicio, $inCodModalidade, $inInscricao, $flValorOrigem, $stDtOrigem, $dtAtual );
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaConsultaMemoriaCalculoGenerico($inCodInscricao, $inExercicio, $inCodModalidade, $inInscricao, $flValorOrigem, $stDtOrigem, $dtAtual)
    {
        $stSql = "
            SELECT
                aplica_acrescimo_modalidade (
                    0,
                    ".$inCodInscricao.",
                    ".$inExercicio.",
                    ".$inCodModalidade.",
                    0,
                    ".$inInscricao.",
                    ".$flValorOrigem.",
                    '".$stDtOrigem."',
                    '".$dtAtual."',
                    'false'
                ) AS acrescimos
        ";

        return $stSql;
    }

    public function recuperaListaExercicioOriginalParcela(&$rsRecordSet, $inCodParcela, $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stSql = $this->montaRecuperaListaExercicioOriginalParcela( $inCodParcela );
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaListaExercicioOriginalParcela($inCodParcela)
    {
        $stSql = "
            SELECT DISTINCT
                divida_ativa.exercicio_original
            FROM
                arrecadacao.parcela

            INNER JOIN
                arrecadacao.lancamento_calculo
            ON
                lancamento_calculo.cod_lancamento = parcela.cod_lancamento

            INNER JOIN
                divida.parcela_calculo
            ON
                parcela_calculo.cod_calculo = lancamento_calculo.cod_calculo

            INNER JOIN
                divida.divida_parcelamento
            ON
                divida_parcelamento.num_parcelamento = parcela_calculo.num_parcelamento

            INNER JOIN
                divida.divida_ativa
            ON
                divida_ativa.cod_inscricao = divida_parcelamento.cod_inscricao
                AND divida_ativa.exercicio = divida_parcelamento.exercicio

            WHERE
                parcela.cod_parcela = ".$inCodParcela;

        return $stSql;
    }

    public function recuperaConsultaVerificaInscricao(&$rsRecordSet, $stFiltro, $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stSql = $this->montaRecuperaConsultaVerificaInscricao().$stFiltro;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaConsultaVerificaInscricao()
    {
        $stSql = " SELECT divida_ativa.*
                        , parcela.num_parcela
                        , parcelamento.numcgm_usuario

                     FROM divida.divida_ativa

               INNER JOIN divida.divida_parcelamento
                       ON divida_parcelamento.cod_inscricao = divida_ativa.cod_inscricao
                      AND divida_parcelamento.exercicio = divida_ativa.exercicio

               INNER JOIN divida.parcelamento
                       ON divida_parcelamento.num_parcelamento = parcelamento.num_parcelamento

                LEFT JOIN divida.parcela
                       ON parcelamento.num_parcelamento = parcela.num_parcelamento

                LEFT JOIN divida.parcelamento_cancelamento
                       ON parcelamento.num_parcelamento = parcelamento_cancelamento.num_parcelamento

                    WHERE ( ( parcelamento_cancelamento.num_parcelamento IS NULL AND parcelamento.judicial = FALSE ) OR
                            ( parcelamento_cancelamento.num_parcelamento IS NOT NULL AND parcelamento.judicial = TRUE AND parcela.cancelada = TRUE )
                          ) ";

        return $stSql;
    }

    public function recuperaListaInscricoes(&$rsRecordSet, $stCondicao , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stSql = $this->montaRecuperaListaInscricoes().$stCondicao.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaListaInscricoes()
    {
        $stSql  = " SELECT
                        ddc.numcgm,
                        (
                            SELECT
                                sw_cgm.nom_cgm
                            FROM
                                sw_cgm
                            WHERE
                                sw_cgm.numcgm = ddc.numcgm
                        )AS nom_cgm,
                        (
                            SELECT
                                min(divida_parcelamento.num_parcelamento)
                            FROM
                                divida.divida_parcelamento
                            WHERE
                                divida_parcelamento.cod_inscricao = dda.cod_inscricao
                                AND divida_parcelamento.exercicio = dda.exercicio
                        )AS num_parcelamento,
                        dda.cod_inscricao,
                        dda.exercicio,
                        to_char(dda.dt_inscricao, 'dd/mm/yyyy') AS dt_inscricao_divida

                    FROM
                        divida.divida_ativa AS dda

                    INNER JOIN
                        divida.divida_cgm AS ddc
                    ON
                        dda.cod_inscricao = ddc.cod_inscricao
                        AND dda.exercicio = ddc.exercicio

                    LEFT JOIN
                        divida.divida_imovel AS ddi
                    ON
                        ddi.cod_inscricao = ddc.cod_inscricao
                        AND ddi.exercicio = ddc.exercicio

                    LEFT JOIN
                        divida.divida_empresa AS dde
                    ON
                        dde.cod_inscricao = ddc.cod_inscricao
                        AND dde.exercicio = ddc.exercicio \n";

        return $stSql;
    }

    public function criaTabelaTodasParcelas($boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stSql = $this->montaCriaTabelaTodasParcelas();
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaCriaTabelaTodasParcelas()
    {
        $stSql  = " DROP TABLE IF EXISTS tmp_todas_parcelas;
                    CREATE TABLE tmp_todas_parcelas AS (
                        SELECT
                                max(carne.numeracao)::varchar as numeracao
                                , carne.cod_convenio
                                , carne.exercicio::int
                                , ap.cod_parcela
                                , alc.cod_calculo
                                , alc.cod_lancamento
                                , nr_parcela
                                , calc.cod_credito
                                , mon.descricao_credito
                                , mon.cod_natureza
                                , mon.cod_genero
                                , mon.cod_especie
                                , (alc.valor * arrecadacao.calculaProporcaoParcela(ap.cod_parcela))::numeric(14,2) as valor
                                , (alc.valor * arrecadacao.calculaProporcaoParcela(ap.cod_parcela)) as valor_exato
                        FROM
                            arrecadacao.parcela as ap
                        JOIN arrecadacao.carne
                            ON carne.cod_parcela = ap.cod_parcela
                        JOIN arrecadacao.lancamento_calculo as alc
                            ON alc.cod_lancamento = ap.cod_lancamento
                        JOIN arrecadacao.calculo as calc
                            ON calc.cod_calculo = alc.cod_calculo
                        JOIN monetario.credito as mon
                            ON mon.cod_credito = calc.cod_credito
                            AND mon.cod_natureza = calc.cod_natureza
                            AND mon.cod_especie = calc.cod_especie
                            AND mon.cod_genero = calc.cod_genero
                        
                        LEFT JOIN ( SELECT carne.cod_parcela
                                    FROM arrecadacao.carne
                                    JOIN arrecadacao.pagamento
                                      ON pagamento.numeracao    = carne.numeracao
                                     AND pagamento.cod_convenio = carne.cod_convenio
                        ) AS apag
                            ON apag.cod_parcela = carne.cod_parcela
                        
                        LEFT JOIN   arrecadacao.carne_devolucao as carned
                            ON carned.numeracao = carne.numeracao and carned.cod_convenio = carne.cod_convenio
                        WHERE
            
                        case when carned.numeracao is not null then
                            case when 1 < (select count(*) from arrecadacao.carne_devolucao acd where acd.numeracao = carne.numeracao
                                                                                             and acd.cod_convenio = carne.cod_convenio) then
                                false
                            else
                                carned.cod_motivo = 10
                            end
                        else
                            true 
                        end
            
                        AND apag.cod_parcela IS NULL
                        AND ap.cod_lancamento IN (".$this->getDado('stCodLancamentos').")
                    GROUP BY
                        carne.exercicio
                        , carne.cod_convenio
                        , ap.cod_parcela
                        , alc.cod_calculo
                        , alc.cod_lancamento
                        , nr_parcela
                        , alc.valor
                        , calc.cod_credito
                        , mon.descricao_credito
                        , mon.cod_natureza
                        , mon.cod_genero
                        , mon.cod_especie
                    ORDER BY
                    ap.cod_parcela, alc.cod_calculo
            );
        ";
        return $stSql;
    }

    public function deletaTabelaParcelas($boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stSql = $this->montaDeletaTabelaParcelas();
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaDeletaTabelaParcelas()
    {
        $stSql  = "DROP TABLE IF EXISTS tmp_todas_parcelas;";

        return $stSql;
    }

    public function recuperaTimestampInsert(&$rsRecordSet, $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stSql = $this->montaRecuperaTimestampInsert();
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaTimestampInsert()
    {
        $stSql  = " SELECT now()::TIMESTAMP WITHOUT TIME ZONE as timestamp_insert;";
        return $stSql;
    }



}// end of class

?>
