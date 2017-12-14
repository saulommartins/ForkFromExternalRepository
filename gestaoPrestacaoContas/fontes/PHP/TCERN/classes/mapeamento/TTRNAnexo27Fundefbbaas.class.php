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
    * Extensão da Classe de mapeamento
    * Data de Criação   : 17/08/2014

    * @author Desenvolvedor: Michel Teixeira

    * @ignore

    $Id: TTRNAnexo27Fundefbbaas.class.php 60030 2014-09-25 19:20:35Z michel $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CLA_PERSISTENTE;

class TTRNAnexo27Fundefbbaas extends Persistente
{
    public function TTRNAnexo27Fundefbbaas()
    {
        $this->setEstrutura( array() );
        $this->setEstruturaAuxiliar( array() );
        $this->setDado('exercicio',Sessao::getExercicio());
    }

    public function montaRecuperaRelacionamento()
    {
        $stSql = "
               SELECT contrato.cod_contrato
                    , contrato.registro as matricula
                    , sw_cgm.nom_cgm as nome
                    , sw_cgm_pessoa_fisica.cpf as cpf
                    , sw_cgm_pessoa_fisica.rg as id
                    , servidor.nr_titulo_eleitor
                    , to_char(sw_cgm_pessoa_fisica.dt_nascimento,'dd/mm/yyyy') as dt_nascimento
                    , CASE WHEN sexo = 'f' THEN 'F' 
                           ELSE 'M'
                      END AS sexo
                    -- de-para do grau de instrucao, é fixo.
                    , CASE WHEN sw_cgm_pessoa_fisica.cod_escolaridade IN (1)     THEN '01'
                           WHEN sw_cgm_pessoa_fisica.cod_escolaridade IN (2,4)   THEN '02'
                           WHEN sw_cgm_pessoa_fisica.cod_escolaridade IN (5)     THEN '03'
                           WHEN sw_cgm_pessoa_fisica.cod_escolaridade IN (6)     THEN '04'
                           WHEN sw_cgm_pessoa_fisica.cod_escolaridade IN (7)     THEN '05'
                           WHEN sw_cgm_pessoa_fisica.cod_escolaridade IN (8)     THEN '07'
                           WHEN sw_cgm_pessoa_fisica.cod_escolaridade IN (9)     THEN '08'
                           WHEN sw_cgm_pessoa_fisica.cod_escolaridade IN (10,11) THEN '10'
                           ELSE '11'
                      END as grau_instrucao                                           
                   
                    -- de-para do campo codigo_tipo da tabela cargo. Necessário buscar os codigos da configuração do SIAI                            
                    , (
                        SELECT cod_siai FROM tcern.sub_divisao_descricao_siai WHERE cod_entidade=".$this->getDado('inCodEntidade')." AND exercicio='".Sessao::getExercicio()."' AND cod_sub_divisao=ultimo_contrato_servidor_sub_divisao_funcao.cod_sub_divisao_funcao
                      ) as cod_tipo_cargo                                                                                 
                    , ultimo_contrato_servidor_sub_divisao_funcao.cod_sub_divisao_funcao||''||cargo.cod_cargo as cod_cargo
                    , cargo.descricao as descricao_cargo                                                                                    
                    , vw_orgao_nivel.orgao as codigo_lotacao                             
                    , recuperaDescricaoOrgao(ultimo_contrato_servidor_orgao.cod_orgao, to_date((select pega0datafinalcompetenciadoperiodomovimento(".$this->getDado('inCodMovimentacao').")), 'yyyy-mm-dd')) as descricao_lotacao                        
                    , 1 as vinculo
                    , CASE WHEN recuperarsituacaodocontrato(contrato.cod_contrato, '".$this->getDado('inCodMovimentacao')."', '".$this->getDado('stEntidade')."') = 'R' THEN 2
                           WHEN recuperarsituacaodocontrato(contrato.cod_contrato, '".$this->getDado('inCodMovimentacao')."', '".$this->getDado('stEntidade')."') = 'P' THEN 3
                           ELSE 1
                      END as situacaofuncional
                    , padrao.cod_padrao as codigo_nivel_funcional
                    , padrao.descricao  as descricao_nivel_funcional
                    -- de-para (fixo) para encontrar o valor do campo FormaIngresso. 
                    , CASE WHEN cod_vinculo='30' AND cod_categoria = '21' THEN '01'
                           WHEN cod_vinculo='35' AND cod_categoria = '19' THEN '02'
                           WHEN cod_vinculo='10' AND cod_categoria = '01' THEN '03'
                           WHEN cod_vinculo='30' AND cod_categoria = '20' THEN '05'
                           WHEN cod_vinculo='35' AND cod_categoria = '20' THEN '07'
                           WHEN cod_vinculo='30' AND cod_categoria = '19' THEN '08'
                           ELSE '12'
                      END as forma_ingresso
                    , to_char(ultimo_contrato_servidor_nomeacao_posse.dt_admissao::date,'dd/mm/yyyy') as dt_admissao      
                    -- de-para forma de afastamento (fixo)
                    , CASE WHEN caso_causa.cod_causa_rescisao IN (12)           THEN '01'
                           WHEN caso_causa.cod_causa_rescisao IN (10,11,20,21)  THEN '02'
                           WHEN caso_causa.cod_causa_rescisao IN (30,31)        THEN '03'
                           WHEN caso_causa.cod_causa_rescisao between 70 AND 79 THEN '06'
                           WHEN caso_causa.cod_causa_rescisao IN (50)           THEN '07'
                           WHEN caso_causa.cod_causa_rescisao between 60 AND 64 THEN '09'
                           WHEN caso_causa.cod_causa_rescisao IS NOT NULL       THEN '10'
                      END as forma_afastamento
                    , to_char(ultimo_contrato_servidor_caso_causa.dt_rescisao,'dd/mm/yyyy') as dt_afastamento
                    , REPLACE(COALESCE( vencimento_base.valor,000   )::VARCHAR, '.', '') AS vencimento_base
                    , REPLACE(COALESCE( outras_vantagens.valor,000  )::VARCHAR, '.', '') AS outras_vantagens
                    , REPLACE(COALESCE( INSS.valor,000              )::VARCHAR, '.', '') AS inss
                    , REPLACE(COALESCE( IRRF.valor,000              )::VARCHAR, '.', '') AS irrf
                    , REPLACE(COALESCE( outros_descontos.valor,000  )::VARCHAR, '.', '') AS outros_descontos
                 FROM pessoal.contrato                         
           INNER JOIN pessoal.contrato_servidor
                   ON contrato_servidor.cod_contrato = contrato.cod_contrato
           INNER JOIN pessoal.servidor_contrato_servidor
                   ON contrato_servidor.cod_contrato = servidor_contrato_servidor.cod_contrato
           INNER JOIN pessoal.servidor
                   ON servidor_contrato_servidor.cod_servidor = servidor.cod_servidor
           INNER JOIN sw_cgm
                   ON servidor.numcgm = sw_cgm.numcgm
           INNER JOIN sw_cgm_pessoa_fisica 
                   ON sw_cgm_pessoa_fisica.numcgm=sw_cgm.numcgm
           INNER JOIN ultimo_contrato_servidor_orgao('".$this->getDado('stEntidade')."', '".$this->getDado('inCodMovimentacao')."') as ultimo_contrato_servidor_orgao
                   ON contrato_servidor.cod_contrato = ultimo_contrato_servidor_orgao.cod_contrato
           INNER JOIN ultimo_contrato_servidor_nomeacao_posse('".$this->getDado('stEntidade')."', '".$this->getDado('inCodMovimentacao')."') as ultimo_contrato_servidor_nomeacao_posse
                   ON contrato_servidor.cod_contrato = ultimo_contrato_servidor_nomeacao_posse.cod_contrato
           INNER JOIN ultimo_contrato_servidor_funcao('".$this->getDado('stEntidade')."', '".$this->getDado('inCodMovimentacao')."') as ultimo_contrato_servidor_funcao
                   ON contrato_servidor.cod_contrato = ultimo_contrato_servidor_funcao.cod_contrato
           INNER JOIN pessoal.cargo 
                   ON cargo.cod_cargo = ultimo_contrato_servidor_funcao.cod_cargo
           INNER JOIN ultimo_contrato_servidor_regime_funcao('".$this->getDado('stEntidade')."', '".$this->getDado('inCodMovimentacao')."') as ultimo_contrato_servidor_regime_funcao
                   ON contrato_servidor.cod_contrato = ultimo_contrato_servidor_regime_funcao.cod_contrato
           INNER JOIN ultimo_contrato_servidor_sub_divisao_funcao('".$this->getDado('stEntidade')."', '".$this->getDado('inCodMovimentacao')."') as ultimo_contrato_servidor_sub_divisao_funcao
                   ON contrato_servidor.cod_contrato = ultimo_contrato_servidor_sub_divisao_funcao.cod_contrato
           INNER JOIN organograma.vw_orgao_nivel
                   ON vw_orgao_nivel.cod_orgao = ultimo_contrato_servidor_orgao.cod_orgao  
           INNER JOIN ultimo_contrato_servidor_padrao('".$this->getDado('stEntidade')."', '".$this->getDado('inCodMovimentacao')."') as ultimo_contrato_servidor_padrao
                   ON contrato_servidor.cod_contrato = ultimo_contrato_servidor_padrao.cod_contrato      
           INNER JOIN folhapagamento".$this->getDado('stEntidade').".padrao
                   ON padrao.cod_padrao = ultimo_contrato_servidor_padrao.cod_padrao                                                                                          
            LEFT JOIN ultimo_contrato_servidor_caso_causa('".$this->getDado('stEntidade')."', '".$this->getDado('inCodMovimentacao')."') as ultimo_contrato_servidor_caso_causa
                   ON contrato_servidor.cod_contrato = ultimo_contrato_servidor_caso_causa.cod_contrato
                  AND ultimo_contrato_servidor_caso_causa.dt_rescisao <= (select pega0datafinalcompetenciadoperiodomovimento(".$this->getDado('inCodMovimentacao').")::DATE)
            LEFT JOIN pessoal.caso_causa
                   ON caso_causa.cod_caso_causa = ultimo_contrato_servidor_caso_causa.cod_caso_causa
            LEFT JOIN (
                      select *
                        from tcern.fn_exportacao_anexo27(     '".Sessao::getExercicio()."'
                                                            , ".$this->getDado('inCodMovimentacao')."
                                                            , '".$this->getDado('stEntidade')."'
                                                            , '".$this->getDado('stEventos')."') as tabela
                                                        (     cod_contrato  integer
                                                            , acumulador    text
                                                            , valor         numeric
                                                        )
                       where acumulador = 'VencimentoBase'
                      ) AS vencimento_base
                   ON vencimento_base.cod_contrato=contrato.cod_contrato
            LEFT JOIN (
                      select *
                        from tcern.fn_exportacao_anexo27(     '".Sessao::getExercicio()."'
                                                            , ".$this->getDado('inCodMovimentacao')."
                                                            , '".$this->getDado('stEntidade')."'
                                                            , '".$this->getDado('stEventos')."') as tabela
                                                        (     cod_contrato  integer
                                                            , acumulador    text
                                                            , valor         numeric
                                                        )
                       where acumulador = 'TotalOutrasVantagens'
                      ) AS outras_vantagens
                   ON outras_vantagens.cod_contrato=contrato.cod_contrato
            LEFT JOIN (
                      select *
                        from tcern.fn_exportacao_anexo27(     '".Sessao::getExercicio()."'
                                                            , ".$this->getDado('inCodMovimentacao')."
                                                            , '".$this->getDado('stEntidade')."'
                                                            , '".$this->getDado('stEventos')."') as tabela
                                                        (     cod_contrato  integer
                                                            , acumulador    text
                                                            , valor         numeric
                                                        )
                       where acumulador = 'INSS'
                      ) AS INSS
                   ON INSS.cod_contrato=contrato.cod_contrato
            LEFT JOIN (
                      select *
                        from tcern.fn_exportacao_anexo27(     '".Sessao::getExercicio()."'
                                                            , ".$this->getDado('inCodMovimentacao')."
                                                            , '".$this->getDado('stEntidade')."'
                                                            , '".$this->getDado('stEventos')."') as tabela
                                                        (     cod_contrato  integer
                                                            , acumulador    text
                                                            , valor         numeric
                                                        )
                       where acumulador = 'IRRF'
                      ) AS IRRF
                   ON IRRF.cod_contrato=contrato.cod_contrato
            LEFT JOIN (
                      select *
                        from tcern.fn_exportacao_anexo27(     '".Sessao::getExercicio()."'
                                                            , ".$this->getDado('inCodMovimentacao')."
                                                            , '".$this->getDado('stEntidade')."'
                                                            , '".$this->getDado('stEventos')."') as tabela
                                                        (     cod_contrato  integer
                                                            , acumulador    text
                                                            , valor         numeric
                                                        )
                       where acumulador = 'TotalOutrosDescontos'
                      ) AS outros_descontos
                   ON outros_descontos.cod_contrato=contrato.cod_contrato
                WHERE vencimento_base.valor > 0.00
                  AND vw_orgao_nivel.cod_orgao IN (".$this->getDado('inCodLotacao').")--LOTAÇÃO";

        return $stSql;
    }

}
