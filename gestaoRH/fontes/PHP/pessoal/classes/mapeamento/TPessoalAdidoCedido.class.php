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
    * Classe de mapeamento da tabela pessoal.adido_cedido
    * Data de Criação: 27/09/2006

    * @author Analista: Vandré Miguel Ramos
    * @author Desenvolvedor: Diego Lemos de Souza

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 30566 $
    $Name$
    $Author: souzadl $
    $Date: 2007-06-07 09:41:04 -0300 (Qui, 07 Jun 2007) $

    * Casos de uso: uc-04.04.30
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  pessoal.adido_cedido
  * Data de Criação: 27/09/2006

  * @author Analista: Vandré Miguel Ramos
  * @author Desenvolvedor: Diego Lemos de Souza

  * @package URBEM
  * @subpackage Mapeamento
*/
class TPessoalAdidoCedido extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TPessoalAdidoCedido()
{
    parent::Persistente();
    $this->setTabela("pessoal.adido_cedido");

    $this->setCampoCod('');
    $this->setComplementoChave('cod_contrato,cod_norma,timestamp');

    $this->AddCampo('cod_contrato'           ,'integer'      ,false ,''   ,true,'TPessoalContratoServidor');
    $this->AddCampo('cod_norma'              ,'integer'      ,false ,''   ,true,'TNormasNorma');
    $this->AddCampo('timestamp'              ,'timestamp_now',true  ,''   ,true,false);
    $this->AddCampo('cgm_cedente_cessionario','integer'      ,false ,''   ,false,'TCGMCGM','numcgm');
    $this->AddCampo('dt_inicial'             ,'date'         ,true  ,''   ,false,false);
    $this->AddCampo('dt_final'               ,'date'         ,true  ,''   ,false,false);
    $this->AddCampo('tipo_cedencia'          ,'char'         ,false ,'1'  ,false,false);
    $this->AddCampo('indicativo_onus'        ,'char'         ,false ,'1'  ,false,false);
    $this->AddCampo('num_convenio'           ,'varchar'      ,false ,'15' ,false,false);

}

function montaRecuperaRelacionamento()
{
    $stSql  = "    SELECT adido_cedido.*\n";
    $stSql .= "         , to_char(adido_cedido.dt_inicial,'dd/mm/yyyy') as data_inicial\n";
    $stSql .= "         , to_char(adido_cedido.dt_final,'dd/mm/yyyy') as data_final\n";
    $stSql .= "         , contrato.registro\n";
    $stSql .= "         , sw_cgm.numcgm\n";
    $stSql .= "         , sw_cgm.nom_cgm\n";
    $stSql .= "         , vw_orgao_nivel.orgao\n";
    $stSql .= "         , recuperaDescricaoOrgao(orgao.cod_orgao, '".Sessao::getExercicio()."-01-01') as descricao\n";
    $stSql .= "      FROM pessoal.adido_cedido\n";
    $stSql .= "INNER JOIN pessoal.contrato\n";
    $stSql .= "        ON adido_cedido.cod_contrato = contrato.cod_contrato\n";
    $stSql .= "INNER JOIN pessoal.servidor_contrato_servidor\n";
    $stSql .= "        ON contrato.cod_contrato = servidor_contrato_servidor.cod_contrato\n";
    $stSql .= "INNER JOIN pessoal.servidor\n";
    $stSql .= "        ON servidor_contrato_servidor.cod_servidor = servidor.cod_servidor\n";
    $stSql .= "INNER JOIN sw_cgm\n";
    $stSql .= "        ON servidor.numcgm = sw_cgm.numcgm\n";
    $stSql .= "INNER JOIN pessoal.contrato_servidor_orgao\n";
    $stSql .= "        ON contrato_servidor_orgao.cod_contrato = servidor_contrato_servidor.cod_contrato\n";
    $stSql .= "INNER JOIN organograma.orgao\n";
    $stSql .= "        ON contrato_servidor_orgao.cod_orgao = orgao.cod_orgao\n";

    //$stSql .= "INNER JOIN organograma.orgao_nivel\n";
    //$stSql .= "        ON orgao.cod_orgao = orgao_nivel.cod_orgao\n";
    //$stSql .= "INNER JOIN organograma.nivel\n";
    //$stSql .= "        ON orgao_nivel.cod_nivel = nivel.cod_nivel\n";
    //$stSql .= "       AND orgao_nivel.cod_organograma = nivel.cod_organograma\n";
    //$stSql .= "INNER JOIN organograma.organograma\n";
    //$stSql .= "        ON nivel.cod_organograma = organograma.cod_organograma\n";
    //$stSql .= "INNER JOIN organograma.vw_orgao_nivel\n";
    //$stSql .= "        ON organograma.cod_organograma = vw_orgao_nivel.cod_organograma\n";
    //$stSql .= "       AND orgao.cod_orgao = vw_orgao_nivel.cod_orgao\n";

    $stSql .= "INNER JOIN organograma.vw_orgao_nivel\n";
    $stSql .= "       ON orgao.cod_orgao = vw_orgao_nivel.cod_orgao\n";

    $stSql .= "     WHERE adido_cedido.timestamp = ( SELECT timestamp\n";
    $stSql .= "                                        FROM pessoal.adido_cedido as adido_cedido_interno\n";
    $stSql .= "                                       WHERE adido_cedido_interno.cod_contrato = adido_cedido.cod_contrato\n";
    $stSql .= "                                    ORDER BY timestamp DESC\n";
    $stSql .= "                                       LIMIT 1 )\n";
    $stSql .= "       AND contrato_servidor_orgao.timestamp = ( SELECT timestamp\n";
    $stSql .= "                                                   FROM pessoal.contrato_servidor_orgao as contrato_servidor_orgao_interno\n";
    $stSql .= "                                                  WHERE contrato_servidor_orgao_interno.cod_contrato = contrato_servidor_orgao.cod_contrato\n";
    $stSql .= "                                               ORDER BY timestamp DESC\n";
    $stSql .= "                                                  LIMIT 1 )\n";
    $stSql .= "       AND NOT EXISTS (SELECT *\n";
    $stSql .= "                         FROM pessoal.adido_cedido_excluido\n";
    $stSql .= "                        WHERE adido_cedido_excluido.cod_norma = adido_cedido.cod_norma\n";
    $stSql .= "                          AND adido_cedido_excluido.cod_contrato = adido_cedido.cod_contrato\n";
    $stSql .= "                          AND adido_cedido_excluido.timestamp_cedido_adido = adido_cedido.timestamp)\n";

    return $stSql;
}

function recuperaAdidosCedidosSEFIP(&$rsRecordSet, $stFiltro = "", $stOrdem ="", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stOrdem = ( $stOrdem != "" ) ? " ORDER BY ".$stOrdem : " ORDER BY nom_cgm";
    $stSql  = $this->montaRecuperaAdidosCedidosSEFIP().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaAdidosCedidosSEFIP()
{
    $stSql  = "SELECT adido_cedido.cgm_cedente_cessionario                                                                                                 \n";
    $stSql .= "     , (SELECT cnpj FROM sw_cgm_pessoa_juridica WHERE numcgm = adido_cedido.cgm_cedente_cessionario) as cnpj                                \n";
    $stSql .= "     , sw_cgm.nom_cgm                                                                                                                       \n";
    $stSql .= "     , sw_cgm.logradouro                                                                                                                    \n";
    $stSql .= "     , sw_cgm.numero                                                                                                                        \n";
    $stSql .= "     , sw_cgm.complemento                                                                                                                   \n";
    $stSql .= "     , sw_cgm.bairro                                                                                                                        \n";
    $stSql .= "     , sw_cgm.cep                                                                                                                           \n";
    $stSql .= "     , (SELECT nom_municipio FROM sw_municipio WHERE cod_municipio = sw_cgm.cod_municipio AND cod_uf = sw_cgm.cod_uf) as nom_municipio      \n";
    $stSql .= "     , (SELECT sigla_uf FROM sw_uf WHERE cod_uf = sw_cgm.cod_uf) as sigla                                                                   \n";
    $stSql .= "  FROM pessoal.adido_cedido                                                                                                                 \n";
    $stSql .= "     , (SELECT cod_contrato                                                                                                                 \n";
    $stSql .= "             , max(timestamp) as timestamp                                                                                                  \n";
    $stSql .= "          FROM pessoal.adido_cedido                                                                                                         \n";
    $stSql .= "        GROUP BY cod_contrato) as max_adido_cedido                                                                                          \n";
    $stSql .= "     , sw_cgm                                                                                                                               \n";
    $stSql .= " WHERE adido_cedido.cod_contrato = max_adido_cedido.cod_contrato                                                                            \n";
    $stSql .= "   AND adido_cedido.timestamp    = max_adido_cedido.timestamp                                                                               \n";
    $stSql .= "   AND adido_cedido.cgm_cedente_cessionario = sw_cgm.numcgm                                                                                 \n";
    $stSql .= "   AND NOT EXISTS (SELECT *                                                                                                                 \n";
    $stSql .= "                     FROM pessoal.adido_cedido_excluido                                                                                     \n";
    $stSql .= "                    WHERE adido_cedido_excluido.cod_norma = adido_cedido.cod_norma                                                          \n";
    $stSql .= "                      AND adido_cedido_excluido.cod_contrato = adido_cedido.cod_contrato                                                    \n";
    $stSql .= "                      AND adido_cedido_excluido.timestamp_cedido_adido = adido_cedido.timestamp)                                            \n";
    $stSql .= "   AND ((adido_cedido.tipo_cedencia = 'a' AND indicativo_onus = 'e') or                                                                     \n";
    $stSql .= "        (adido_cedido.tipo_cedencia = 'c' AND indicativo_onus = 'c'))                                                                       \n";
    $stSql .= "GROUP BY adido_cedido.cgm_cedente_cessionario                                                                                               \n";
    $stSql .= "       , sw_cgm.nom_cgm                                                                                                                     \n";
    $stSql .= "       , sw_cgm.logradouro                                                                                                                  \n";
    $stSql .= "       , sw_cgm.numero                                                                                                                      \n";
    $stSql .= "       , sw_cgm.complemento                                                                                                                 \n";
    $stSql .= "       , sw_cgm.bairro                                                                                                                      \n";
    $stSql .= "       , sw_cgm.cep                                                                                                                         \n";
    $stSql .= "       , sw_cgm.cod_municipio                                                                                                               \n";
    $stSql .= "       , sw_cgm.cod_uf                                                                                                                      \n";

    return $stSql;
}

function recuperaAdidosCedidosSEFIPContratos(&$rsRecordSet, $stFiltro = "", $stOrdem ="", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stOrdem = ( $stOrdem != "" ) ? " ORDER BY ".$stOrdem : " ORDER BY cod_contrato";
    $stSql  = $this->montaRecuparaAdidosCedidosSEFIPContratos().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuparaAdidosCedidosSEFIPContratos()
{
    $stSql  = "SELECT adido_cedido.*                                               \n";
    $stSql .= "  FROM pessoal.adido_cedido                                         \n";
    $stSql .= "     , (SELECT cod_contrato                                         \n";
    $stSql .= "             , max(timestamp) as timestamp                          \n";
    $stSql .= "          FROM pessoal.adido_cedido                                 \n";
    $stSql .= "        GROUP BY cod_contrato) as max_adido_cedido                  \n";
    $stSql .= " WHERE adido_cedido.cod_contrato = max_adido_cedido.cod_contrato    \n";
    $stSql .= "   AND adido_cedido.timestamp = max_adido_cedido.timestamp          \n";
    $stSql .= "   AND NOT EXISTS (SELECT *                                                                                                                 \n";
    $stSql .= "                     FROM pessoal.adido_cedido_excluido                                                                                     \n";
    $stSql .= "                    WHERE adido_cedido_excluido.cod_norma = adido_cedido.cod_norma                                                          \n";
    $stSql .= "                      AND adido_cedido_excluido.cod_contrato = adido_cedido.cod_contrato                                                    \n";
    $stSql .= "                      AND adido_cedido_excluido.timestamp_cedido_adido = adido_cedido.timestamp)                                            \n";

    return $stSql;
}

function recuperaAfastamentoDisposicaoEsfinge(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaAfastamentoDisposicaoEsfinge().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaAfastamentoDisposicaoEsfinge()
{
    $stSql = "
select adido_cedido.cod_norma
      ,to_char(norma.dt_assinatura, 'dd/mm/yyyy') as dt_assinatura
      ,to_char(norma.dt_publicacao,'dd/mm/yyyy') as dt_publicacao
      ,norma.descricao as desc_norma
      ,contrato.registro
      ,1 as cod_tipo_quadro
      ,contrato_servidor.cod_cargo
      ,to_char(cargo_criacao.timestamp, 'dd/mm/yyyy') as dt_criacao
      ,recuperaDescricaoOrgao(orgao.cod_orgao, '".Sessao::getExercicio()."-01-01') as desc_lotacao
      ,cgm_cedente.nom_cgm as nom_cedente
      ,cgm_cessionario.nom_cgm as nom_cessionario
      ,case adido_cedido.indicativo_onus
         when 'c' then 'S'
         else 'N'
      end as indicativo_onus
      ,adido_cedido.num_convenio
      ,to_char(adido_cedido.dt_final, 'dd/mm/yyyy') as dt_final
from (select adido_cedido.cod_contrato
            ,adido_cedido.cod_norma
            ,adido_cedido.indicativo_onus
            ,adido_cedido.num_convenio
            ,adido_cedido.dt_inicial
            ,adido_cedido.dt_final
      from pessoal.adido_cedido
          ,(select cod_contrato, cod_norma, max(timestamp) as timestamp
              from pessoal.adido_cedido
             where timestamp < to_date('".$this->getDado('dt_final')."', 'dd/mm/yyyy')
            group by cod_contrato, cod_norma) as ultimo_adido_cedido
      where adido_cedido.cod_norma = ultimo_adido_cedido.cod_norma
        and adido_cedido.cod_contrato = ultimo_adido_cedido.cod_contrato
        and adido_cedido.timestamp = ultimo_adido_cedido.timestamp
) as adido_cedido
join pessoal.contrato
  on contrato.cod_contrato = adido_cedido.cod_contrato
join pessoal.contrato_servidor
  on contrato_servidor.cod_contrato = adido_cedido.cod_contrato
join normas.norma
  on norma.cod_norma = adido_cedido.cod_norma
join (select cod_cargo, min(timestamp) as timestamp
        from pessoal.cargo_sub_divisao
       where timestamp < to_date('".$this->getDado('dt_final')."', 'dd/mm/yyyy')
      group by cod_cargo ) as cargo_criacao
  on contrato_servidor.cod_cargo = cargo_criacao.cod_cargo
join (select contrato_servidor_orgao.cod_orgao, contrato_servidor_orgao.cod_contrato
      from pessoal.contrato_servidor_orgao
          ,(select cod_orgao, max(timestamp) as timestamp
              from pessoal.contrato_servidor_orgao
             where timestamp < to_date('".$this->getDado('dt_final')."', 'dd/mm/yyyy')
            group by cod_orgao) as ultimo_contrato_servidor_orgao
      where contrato_servidor_orgao.cod_orgao = ultimo_contrato_servidor_orgao.cod_orgao
        and contrato_servidor_orgao.timestamp = ultimo_contrato_servidor_orgao.timestamp) as contrato_servidor_orgao
  on contrato_servidor.cod_contrato = contrato_servidor_orgao.cod_contrato
join organograma.orgao
  on orgao.cod_orgao = contrato_servidor_orgao.cod_orgao
join (select adido_cedido.cod_contrato
            ,adido_cedido.cgm_cedente_cessionario as cgm_cedente
            ,adido_cedido.cod_norma
      from pessoal.adido_cedido
          ,(select cod_contrato, cod_norma, max(timestamp) as timestamp
              from pessoal.adido_cedido
             where tipo_cedencia = 'a'
               and timestamp < to_date('".$this->getDado('dt_final')."', 'dd/mm/yyyy')
            group by cod_contrato, cod_norma) as ultimo_adido_cedido
      where adido_cedido.cod_norma = ultimo_adido_cedido.cod_norma
        and adido_cedido.cod_contrato = ultimo_adido_cedido.cod_contrato
        and adido_cedido.timestamp = ultimo_adido_cedido.timestamp
) as cedente
  on cedente.cod_contrato = adido_cedido.cod_contrato
 and cedente.cod_norma = adido_cedido.cod_norma
join (select adido_cedido.cod_contrato
            ,adido_cedido.cgm_cedente_cessionario as cgm_cessionario
            ,adido_cedido.cod_norma
      from pessoal.adido_cedido
          ,(select cod_contrato, cod_norma, max(timestamp) as timestamp
              from pessoal.adido_cedido
             where tipo_cedencia = 'c'
               and timestamp < to_date('".$this->getDado('dt_final')."', 'dd/mm/yyyy')
            group by cod_contrato, cod_norma) as ultimo_adido_cedido
      where adido_cedido.cod_norma = ultimo_adido_cedido.cod_norma
        and adido_cedido.cod_contrato = ultimo_adido_cedido.cod_contrato
        and adido_cedido.timestamp = ultimo_adido_cedido.timestamp
) as cessionario
  on cessionario.cod_contrato = adido_cedido.cod_contrato
 and cessionario.cod_norma = adido_cedido.cod_norma
join sw_cgm as cgm_cedente
  on cedente.cgm_cedente = cgm_cedente.numcgm
join sw_cgm as cgm_cessionario
  on cessionario.cgm_cessionario = cgm_cessionario.numcgm
where adido_cedido.dt_inicial between to_date('".$this->getDado('dt_inicial')."', 'dd/mm/yyyy')
  and to_date('".$this->getDado('dt_final')."', 'dd/mm/yyyy')
";

   return $stSql;
}

}
