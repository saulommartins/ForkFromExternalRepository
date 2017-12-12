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

     * Classe de mapeamento para a tabela IMOBILIARIO.licenca
     * Data de Criação: 18/03/2008

    * @author Analista: Fábio Bertoldi
    * @author Programador: Fernando Piccini Cercato

    * $Id: TCIMLicenca.class.php 60011 2014-09-25 15:12:19Z michel $

     * Casos de uso: uc-05.01.28
*/



include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TCIMLicenca extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function TCIMLicenca()
    {
        parent::Persistente();
        $this->setTabela('imobiliario.licenca');

        $this->setCampoCod('');
        $this->setComplementoChave('cod_licenca, exercicio, timestamp');

        $this->AddCampo( 'cod_licenca', 'integer'  , true  , '', true  , false );
        $this->AddCampo( 'exercicio'  , 'varchar'  , true  , '4', true , true  );
        $this->AddCampo( 'cod_tipo'   , 'integer'  , false , '', false , true  );
        $this->AddCampo( 'numcgm'     , 'integer'  , false , '', false , true  );
        $this->AddCampo( 'timestamp'  , 'timestamp', false , '', false , true  );
        $this->AddCampo( 'dt_inicio'  , 'date'     , true  , '', false , false );
        $this->AddCampo( 'dt_termino' , 'date'     , true  , '', false , false );
        $this->AddCampo( 'observacao' , 'text'     , true  , '', false , false );
    }

    public function recuperaProximaLicenca(&$rsRecordSet, $stFiltro = "", $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql = $this->montaRecuperaProximaLicenca().$stFiltro;
        $this->setDebug( $stSql );

        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaProximaLicenca()
    {
        ;

        $stSQL  = " SELECT
                        max(cod_licenca) AS cod_licenca
                    FROM
                        imobiliario.licenca
                    WHERE
                        exercicio = '".Sessao::getExercicio()."'";

        return $stSQL;
    }

    public function recuperaMaxLicenca(&$rsRecordSet, $stFiltro = "", $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql = $this->montaRecuperaMaxLicenca().$stFiltro;
        $this->setDebug( $stSql );

        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaMaxLicenca()
    {
        $stSQL  = " SELECT
                        max(cod_licenca) AS cod_licenca
                    FROM
                        imobiliario.licenca ";

        return $stSQL;
    }

    public function filtroListaLicencas(&$rsRecordSet, $stFiltro = "", $boTransacao = "")
    {
        $obErro      = new Erro;
        $stGroup ="
            GROUP BY
                        licenca.cod_licenca,
                        licenca.exercicio,
                        licenca.cod_tipo,
                        nom_tipo,
                         inscricao,
                        licenca.observacao,
                        licenca.dt_inicio,
                        licenca.dt_termino,
                        tipo_nova_edificacao,
                        area_imovel,
                        area_lote,
                        processo,  
                        licenca_imovel_unidade_autonoma.cod_construcao, 
                        licenca_imovel_unidade_dependente.cod_construcao, 
                        area_edificacao,
                        nome_tipo_edificacao,
                        localizacao.codigo_composto,
                        localizacao.nom_localizacao,
                        nro_lote,
                        licenca_lote.cod_lote,
                        construcao_outros.descricao,
                        cod_construcao_outros,
                        licenca_baixa.dt_termino";
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql = $this->montaFiltroListaLicencas().$stFiltro.$stGroup;
        $this->setDebug( $stSql );
        //$this->debug();
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaFiltroListaLicencas()
    {
        $stSQL = "  SELECT
                        licenca.cod_licenca,
                        licenca.exercicio,
                        licenca.cod_tipo,
                        (
                            SELECT
                                tipo_licenca.nom_tipo
                            FROM
                                imobiliario.tipo_licenca
                            WHERE
                                tipo_licenca.cod_tipo = licenca.cod_tipo
                        )AS nom_tipo,
                        COALESCE ( licenca_imovel.inscricao_municipal::varchar, localizacao.codigo_composto ) AS inscricao,
                        licenca.observacao,
                        to_char ( licenca.dt_inicio, 'dd/mm/YYYY' ) as dt_inicio,
                        to_char ( licenca.dt_termino, 'dd/mm/YYYY' ) as dt_termino,
                        (
                            SELECT
                                cod_tipo
                            FROM
                                imobiliario.licenca_imovel_nova_edificacao
                            WHERE
                                licenca_imovel_nova_edificacao.inscricao_municipal = licenca_imovel.inscricao_municipal
                                AND licenca_imovel_nova_edificacao.cod_licenca = licenca.cod_licenca
                                AND licenca_imovel_nova_edificacao.exercicio = licenca.exercicio
                        )AS tipo_nova_edificacao,
                        (
                            SELECT
                                licenca_imovel_area.area
                            FROM
                                imobiliario.licenca_imovel_area
                            WHERE
                                licenca_imovel_area.inscricao_municipal = licenca_imovel.inscricao_municipal
                                AND licenca_imovel_area.cod_licenca = licenca.cod_licenca
                                AND licenca_imovel_area.exercicio = licenca.exercicio
                        )as area_imovel,
                        (
                            SELECT
                                licenca_lote_area.area
                            FROM
                                imobiliario.licenca_lote_area
                            WHERE
                                licenca_lote_area.cod_lote = licenca_lote.cod_lote
                                AND licenca_lote_area.cod_licenca = licenca.cod_licenca
                                AND licenca_lote_area.exercicio = licenca.exercicio
                        )as area_lote,
                        (
                            SELECT
                                licenca_processo.cod_processo||'/'||licenca_processo.ano_exercicio
                            FROM
                                imobiliario.licenca_processo
                            WHERE
                                licenca_processo.cod_licenca = licenca.cod_licenca
                                AND licenca_processo.exercicio = licenca.exercicio
                        )as processo,
                        COALESCE( licenca_imovel_unidade_autonoma.cod_construcao, licenca_imovel_unidade_dependente.cod_construcao ) AS cod_construcao,
                        CASE WHEN licenca_imovel_unidade_autonoma.cod_construcao IS NOT NULL THEN
                            imobiliario.fn_calcula_area_unidade_autonoma( licenca_imovel.inscricao_municipal, licenca_imovel_unidade_autonoma.cod_construcao )
                        ELSE
                            CASE WHEN licenca_imovel_unidade_dependente.cod_construcao IS NOT NULL THEN
                                imobiliario.fn_calcula_area_unidade_dependente( licenca_imovel.inscricao_municipal, licenca_imovel_unidade_dependente.cod_construcao )
                            END
                        END AS area_edificacao,
                        (
                            SELECT
                                nom_tipo

                            FROM
                                imobiliario.tipo_edificacao

                            INNER JOIN
                                imobiliario.construcao_edificacao
                            ON
                                construcao_edificacao.cod_tipo = tipo_edificacao.cod_tipo
                                AND construcao_edificacao.cod_construcao = COALESCE( licenca_imovel_unidade_autonoma.cod_construcao, licenca_imovel_unidade_dependente.cod_construcao )
                        ) AS nome_tipo_edificacao,
                        localizacao.codigo_composto,
                        localizacao.nom_localizacao,
                        lote_localizacao.valor AS nro_lote,
                        licenca_lote.cod_lote,
                        construcao_outros.descricao,
                        construcao_outros.cod_construcao as cod_construcao_outros,
                        licenca_baixa.dt_termino
                    FROM
                        imobiliario.licenca

                    LEFT JOIN (  SELECT cod_licenca
                                                , exercicio
                                                , dt_inicio
                                                , cod_tipo
                                                , MAX(dt_termino) as dt_termino
                                 FROM imobiliario.licenca_baixa
                             GROUP BY cod_licenca
                                     , exercicio
                                     , dt_inicio
                                     , cod_tipo
                               ) licenca_baixa
                             ON licenca_baixa.cod_licenca = licenca.cod_licenca
                           AND licenca_baixa.exercicio = licenca.exercicio

                    LEFT JOIN
                        imobiliario.licenca_imovel
                    ON
                        licenca_imovel.cod_licenca = licenca.cod_licenca
                        AND licenca_imovel.exercicio = licenca.exercicio

                    LEFT JOIN
                        imobiliario.licenca_lote
                    ON
                        licenca_lote.cod_licenca = licenca.cod_licenca
                        AND licenca_lote.exercicio = licenca.exercicio

                    LEFT JOIN
                        imobiliario.imovel_lote
                    ON
                        imovel_lote.cod_lote = licenca_lote.cod_lote
                        AND imovel_lote.inscricao_municipal = licenca_imovel.inscricao_municipal

                    LEFT JOIN
                        imobiliario.licenca_imovel_unidade_autonoma
                    ON
                        licenca_imovel_unidade_autonoma.cod_licenca = licenca.cod_licenca
                        AND licenca_imovel_unidade_autonoma.exercicio = licenca.exercicio
                        AND licenca_imovel_unidade_autonoma.inscricao_municipal = licenca_imovel.inscricao_municipal

                    LEFT JOIN
                        imobiliario.licenca_imovel_unidade_dependente
                    ON
                        licenca_imovel_unidade_dependente.cod_licenca = licenca.cod_licenca
                        AND licenca_imovel_unidade_dependente.exercicio = licenca.exercicio
                        AND licenca_imovel_unidade_dependente.inscricao_municipal = licenca_imovel.inscricao_municipal
                        
                     LEFT JOIN
                        imobiliario.licenca_imovel_nova_construcao
                    ON
                        licenca_imovel_nova_construcao.cod_licenca = licenca.cod_licenca
                        AND licenca_imovel_nova_construcao.exercicio = licenca.exercicio
                        AND licenca_imovel_nova_construcao.inscricao_municipal = licenca_imovel.inscricao_municipal
                        
                    LEFT JOIN
                        imobiliario.construcao
                    ON
                        construcao.cod_construcao = licenca_imovel_nova_construcao.cod_construcao                      
                   
                   LEFT JOIN
                        imobiliario.construcao_outros
                   ON
                        construcao_outros.cod_construcao = construcao.cod_construcao
                   
                   LEFT JOIN
                        imobiliario.lote_localizacao
                    ON
                        imobiliario.lote_localizacao.cod_lote = imobiliario.licenca_lote.cod_lote
                    
                    LEFT JOIN
                        imobiliario.localizacao
                    ON
                        localizacao.cod_localizacao = lote_localizacao.cod_localizacao ";

        return $stSQL;
    }
    
    function recuperaLicencas(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaLicencas();
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );
    return $obErro;
}

function montaRecuperaLicencas()
{
    $stFiltro="";
    $stSql = "  SELECT licenca.cod_licenca::varchar || '/' || lpad( licenca.exercicio, 4, '0')::varchar as licenca   
                                 , licenca_imovel.inscricao_municipal as inscricao
                                , TO_CHAR(licenca.dt_inicio,'DD/MM/YYYY') as dt_inicio                                    
                                , TO_CHAR(licenca.dt_termino,'DD/MM/YYYY') as dt_termino                                  
                                , licenca_situacao.situacao 
                                , tipo_licenca.nom_tipo
                                , lpad( licenca.exercicio, 4, '0')::varchar as exercicio                                   
                                , modelo_documento.nome_documento
                                , licenca.cod_licenca
                        FROM imobiliario.licenca         
                        
              INNER JOIN imobiliario.permissao 
                          ON permissao.cod_tipo = licenca.cod_tipo
                        AND permissao.numcgm = licenca.numcgm
                        AND permissao.timestamp = licenca.timestamp
                        
              INNER JOIN imobiliario.tipo_licenca
                          ON tipo_licenca.cod_tipo = permissao.cod_tipo       
                  
                INNER JOIN imobiliario.licenca_documento
                            ON licenca_documento.cod_licenca = licenca.cod_licenca
                          AND licenca_documento.exercicio = licenca.exercicio
                          
                INNER JOIN administracao.modelo_documento
                            ON modelo_documento.cod_documento =  licenca_documento.cod_documento
                          AND modelo_documento.cod_tipo_documento = licenca_documento.cod_tipo_documento
                           
               INNER JOIN imobiliario.licenca_imovel
                            ON licenca_imovel.cod_licenca = licenca.cod_licenca
                          AND licenca_imovel.exercicio = licenca.exercicio
                            
               INNER JOIN ( SELECT cod_licenca
                                 , exercicio
                                 , dt_termino
                                 , CASE WHEN imobiliario.fn_consulta_situacao_licenca(cod_licenca, exercicio) = '' AND dt_termino < now()::date THEN
                                                'Vencida'::varchar                                                    
                                   WHEN imobiliario.fn_consulta_situacao_licenca(cod_licenca, exercicio) != '' THEN
                                                imobiliario.fn_consulta_situacao_licenca(cod_licenca, exercicio)
                                   END AS situacao
                              FROM imobiliario.licenca
                          ) as licenca_situacao
                       ON licenca_situacao.cod_licenca = licenca.cod_licenca
                      AND licenca_situacao.exercicio = licenca.exercicio

        ";
        
        if( $this->getDado("stSituacao") != 'Todas'){
            $stFiltro .= "licenca_situacao.situacao ='".$this->getDado("stSituacao")."' AND ";
        }
     
        if ( $this->getDado("stDataInicial") != ''  && $this->getDado("stDataFinal") != '' ) {
            $stFiltro .= " licenca.dt_inicio >= TO_DATE( '".$this->getDado('stDataInicial')."', 'dd/mm/yyyy' )  AND (licenca.dt_termino <= TO_DATE( '".$this->getDado('stDataFinal')  ."', 'dd/mm/yyyy') OR licenca.dt_termino IS NULL  ) AND";
        } 
        
        if ( $this->getDado("inCodImovel")){
             $stFiltro .= " licenca_imovel.inscricao_municipal = ".$this->getDado("inCodImovel")." AND ";
        }
        
        if($this->getDado("stLicenca") != '' && $this->getDado("exercicio") != '' ){
            $stFiltro .= " licenca.cod_licenca = ".$this->getDado("stLicenca")." AND licenca.exercicio= '".$this->getDado("exercicio")."' AND ";
        } else if($this->getDado("stLicenca") != '' && $this->getDado("exercicio") == '' ){
            $stFiltro .= " licenca.cod_licenca = ".$this->getDado("stLicenca")." AND ";
        } else if($this->getDado("stLicenca") == '' && $this->getDado("exercicio") != '' ){
             $stFiltro .= " licenca.exercicio= '".$this->getDado("exercicio")."' AND ";
        }
   
       if ($stFiltro) {
            $stSql.= " WHERE ".substr( $stFiltro, 0, strlen($stFiltro) - 4 );
       }
   
    $stSql .= "  GROUP BY  licenca   
                                  , inscricao
                                  , licenca.dt_inicio                                    
                                  , licenca.dt_termino                                  
                                  , situacao
                                 , licenca.exercicio       
                                 , tipo_licenca.nom_tipo
                                 , modelo_documento.nome_documento
                                 , licenca.cod_licenca
                   ORDER BY  licenca.cod_licenca  ";
                               
    return $stSql;

}
}
