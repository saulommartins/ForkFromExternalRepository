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
  * Mapeamento da tabela frota.veiculo
  * Data de criação : 13/03/2005

    * @author Analista:
    * @author Programador: Fernando Zank Correa Evangelista

    $Revision: 27758 $
    $Name$
    $Author: hboaventura $
    $Date: 2008-01-28 07:15:55 -0200 (Seg, 28 Jan 2008) $

    Caso de uso: uc-03.02.10
**/

/*
$Log$
Revision 1.9  2006/07/06 13:57:42  diego
Retirada tag de log com erro.

Revision 1.8  2006/07/06 12:11:17  diego

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TFrotaVeiculo extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function TFrotaVeiculo()
    {
        parent::Persistente();
        $this->setTabela('frota.veiculo');
        $this->setCampoCod('cod_veiculo');
        $this->setComplementoChave('');
        $this->AddCampo('cod_veiculo'       ,'integer',true,'',true,false);
        $this->AddCampo('cod_marca'         ,'integer',true,'',false,true);
        $this->AddCampo('cod_modelo'        ,'integer',true,'',false,true);
        $this->AddCampo('cod_tipo_veiculo'  ,'integer',true,'',false,true);
        $this->AddCampo('cod_categoria'     ,'integer',true,'',false,true);
        $this->AddCampo('prefixo'           ,'varchar',true,'15',false,false);
        $this->AddCampo('chassi'            ,'varchar',true,'30',false,false);
        $this->AddCampo('dt_aquisicao'      ,'date',true,'',false,false);
        $this->AddCampo('km_inicial'        ,'double',false,'',false,false);
        $this->AddCampo('num_certificado'   ,'integer',false,'',false,false);
        $this->AddCampo('placa'             ,'varchar',true,'7',false,false);
        $this->AddCampo('ano_fabricacao'    ,'varchar',true,'4',false,false);
        $this->AddCampo('ano_modelo'        ,'varchar',true,'4',false,false);
        $this->AddCampo('categoria'         ,'varchar',true,'20',false,false);
        $this->AddCampo('cor','varchar'     ,true,'20',false,false);
        $this->AddCampo('capacidade'        ,'varchar',true,'20',false,false);
        $this->AddCampo('potencia'          ,'varchar',true,'20',false,false);
        $this->AddCampo('cilindrada','varchar',true,'20',false,false);
        $this->AddCampo('num_passageiro'    ,'integer',false,'',false,false);
        $this->AddCampo('capacidade_tanque' ,'integer',false,'',false,false);
    }

    public function recuperaControleIndividualVeiculo(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql = $this->montaRecuperaControleIndividualVeiculo().$stFiltro.$stOrdem;
        $this->stDebug = $stSql;
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function recuperaControleQuilometragem(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql = $this->montaRecuperaControleQuilometragem().$stFiltro.$stOrdem;
        $this->stDebug = $stSql;
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }
    public function montaRecuperaControleIndividualVeiculo()
    {
        $stSql  = "SELECT                                            \n";
        $stSql  .="     v.placa                    as placa          \n";
        $stSql  .="     ,mo.nom_modelo             as modelo         \n";
        $stSql  .="     ,ma.nom_marca              as marca          \n";
        $stSql  .="     ,c.nom_combustivel         as combustivel    \n";
        $stSql  .="     ,v.potencia                as potencia       \n";
        $stSql  .="     ,v.cilindrada              as cilindrada     \n";
        $stSql  .="     ,l.nom_local               as lotado         \n";
        $stSql  .="     ,v.dt_aquisicao            as data_aquisicao \n";
        $stSql  .="     ,v.ano_fabricacao          as ano_fabricacao \n";
    //    $stSql  .="     ,v.nota_fiscal             as nota_fiscal    \n";
        $stSql  .="     ,v.num_certificado         as renavam        \n";
        $stSql  .="     ,tv.nom_tipo               as tipo           \n";
        $stSql  .="     ,v.prefixo                 as prefixo        \n";
        $stSql  .="     ,v.cod_veiculo             as cod_veiculo    \n";
        $stSql  .="     ,um.nom_unidade            as unidade_medida \n";
        $stSql  .="FROM                                              \n";
        $stSql  .="    frota.modelo               as mo             \n";
        $stSql  .="    ,frota.marca                as ma             \n";
        $stSql  .="    ,frota.tipo_veiculo         as tv             \n";
        $stSql  .="    ,frota.combustivel          as c              \n";
        $stSql  .="    ,administracao.local        as l              \n";
        $stSql  .="    ,frota.veiculo              as v              \n";
        $stSql .=" left  join frota.veiculo_combustivel  as vc on ( v.cod_veiculo  = vc.cod_veiculo   ) \n";
        $stSql .=" left join frota.item                 as i   on ( vc.cod_item    = i.cod_item  ) \n";
        $stSql .=" left join almoxarifado.catalogo_item as ci  on ( i.cod_item     = ci.cod_item ) \n";
        $stSql .=" left join administracao.unidade_medida as um on( ci.cod_grandeza = um.cod_grandeza and ci.cod_unidade = um.cod_unidade) \n";
        $stSql  .="WHERE                                             \n";
        $stSql  .="    v.cod_modelo           = mo.cod_modelo        \n";
        $stSql  .="    AND v.cod_marca        = mo.cod_marca         \n";
        $stSql  .="    AND v.cod_combustivel  = c.cod_combustivel    \n";
        $stSql  .="    AND v.cod_local        = l.cod_local          \n";
        $stSql  .="    AND v.cod_setor        = l.cod_setor          \n";
        $stSql  .="    AND v.cod_departamento = l.cod_departamento   \n";
        $stSql  .="    AND v.cod_unidade      = l.cod_unidade        \n";
        $stSql  .="    AND v.cod_orgao        = l.cod_orgao          \n";
        $stSql  .="    AND v.ano_exercicio    = l.ano_exercicio      \n";
        $stSql  .="    AND v.cod_tipo_veiculo = tv.cod_tipo          \n";
        $stSql .="     AND mo.cod_marca       = ma.cod_marca         \n";

        return $stSql;

    }

    public function montaRecuperaControleQuilometragem()
    {
        $stSql  ="SELECT                                                                                                        \n";
        $stSql .="       v.cod_veiculo                                                                           as codigo      \n";
        $stSql .="      ,substr(v.placa,0,4)||'-'||substr(v.placa,4,8)||' - '||ma.nom_marca||', '||mo.nom_modelo as veiculo     \n";
        $stSql .="      ,v.cilindrada                                                                            as cilindrada  \n";
        $stSql .="      ,ci.descricao                                                                            as combustivel \n";
        $stSql .="                                                                                                              \n";
        $stSql .="      ,CASE WHEN                                                                                              \n";
        $stSql .="          v.cod_veiculo in (SELECT cod_veiculo FROM frota.terceiros) THEN                                     \n";
        $stSql .="              'V.TERCEIRO'                                \n";
        $stSql .="          ELSE                                            \n";
        $stSql .="              (SELECT                                     \n";
        $stSql .="                  s.nom_situacao                          \n";
        $stSql .="              FROM                                        \n";
        $stSql .="                   frota.proprio                  as p    \n";
        $stSql .="                  ,patrimonio.bem                 as b    \n";
        $stSql .="                  ,patrimonio.historico_bem       as h    \n";
        $stSql .="                  ,patrimonio.vw_ultimo_historico as u    \n";
        $stSql .="                  ,patrimonio.situacao_bem        as s    \n";
        $stSql .="              WHERE                                       \n";
        $stSql .="                      v.cod_veiculo = p.cod_veiculo       \n";
        $stSql .="                  AND p.cod_bem = b.cod_bem               \n";
        $stSql .="                  AND u.timestamp = h.timestamp           \n";
        $stSql .="                  AND u.cod_bem = h.cod_bem               \n";
        $stSql .="                  AND h.cod_bem = b.cod_bem               \n";
        $stSql .="                  AND u.cod_bem = b.cod_bem               \n";
        $stSql .="                  AND s.cod_situacao = h.cod_situacao     \n";
        $stSql .="              ORDER BY                                    \n";
        $stSql .="                  p.cod_bem )                             \n";
        $stSql .="      END           as situacao                           \n";
        $stSql .="                                                          \n";
        $stSql .="      ,min(m.km)          as km_inicial                   \n";
        $stSql .="      ,max(m.km)          as km_final                     \n";
        $stSql .="      ,sum(mi.quantidade) as quantidade                   \n";
        $stSql .="      ,trim(upper(um.simbolo)) as unidade_medida          \n";
        $stSql .="      ,sum(mi.valor)      as valor                        \n";
        $stSql .="      ,sum(mi.valor)/sum(mi.quantidade)  as valor_medio   \n";
        $stSql .="FROM                                                      \n";
        $stSql .="      frota.veiculo              as v                     \n";
        $stSql .="     ,frota.tipo_veiculo         as tv                    \n";
        $stSql .="     ,frota.modelo               as mo                    \n";
        $stSql .="     ,frota.marca                as ma                    \n";
        $stSql .="     ,frota.manutencao           as m                     \n";
        $stSql .="     ,frota.manutencao_item      as mi                    \n";
        $stSql .="     ,frota.item                 as i                     \n";
        $stSql .="     ,frota.tipo_item            as ti                    \n";
        $stSql .="     ,almoxarifado.catalogo_item as ci                    \n";
        $stSql .="     ,administracao.unidade_medida as um                  \n";
        $stSql .="WHERE                                                     \n";
        $stSql .="        v.cod_marca      = mo.cod_marca                   \n";
        $stSql .="    AND v.cod_modelo     = mo.cod_modelo                  \n";
        $stSql .="    AND mo.cod_marca     = ma.cod_marca                   \n";
        $stSql .="    AND v.cod_veiculo    = m.cod_veiculo                  \n";
        $stSql .="    AND m.cod_manutencao = mi.cod_manutencao              \n";
        $stSql .="    AND m.exercicio      = mi.exercicio                   \n";
        $stSql .="    AND mi.cod_item      = i.cod_item                     \n";
        $stSql .="    AND i.cod_tipo       = ti.cod_tipo                    \n";
        $stSql .="    AND tv.cod_tipo      = v.cod_tipo_veiculo             \n";
        $stSql .="    AND ti.cod_tipo      = 1                              \n";
        $stSql .="    AND i.cod_item       = ci.cod_item                    \n";
        $stSql .="    AND um.cod_grandeza  = ci.cod_grandeza                \n";
        $stSql .="    AND um.cod_unidade   = ci.cod_unidade                 \n";
    //filtro do mes
        $stSql .="    AND m.dt_manutencao between to_date('".$this->getDado("stDataInicial")."','dd/mm/yyyy')  and to_date('".$this->getDado("stDataFinal")."','dd/mm/yyyy')      \n";
        if ($this->getDado('inCodVeiculo') != null)
            $stSql .="    AND v.cod_veiculo = ".$this->getDado("inCodVeiculo")."                   \n";
        if ($this->getDado('inCodOrigemVeiculo') == 3 )
            $stSql .="    AND v.cod_veiculo in (SELECT cod_veiculo from frota.terceiros)           \n";
        if ($this->getDado('inCodOrigemVeiculo') == 2 )
            $stSql .="    AND v.cod_veiculo in (SELECT cod_veiculo from frota.proprio)             \n";
        if ($this->getDado('inCodVeiculoBaixado') == 2 )
            $stSql .="    AND v.cod_veiculo in (select cod_veiculo from frota.veiculo_baixado)     \n";
        if ($this->getDado('inCodVeiculoBaixado') == 3 )
            $stSql .="    AND v.cod_veiculo not in (select cod_veiculo from frota.veiculo_baixado) \n";
        if ($this->getDado('stPrefixo') != null)
            $stSql .="    AND v.prefixo = '".$this->getDado("stPrefixo")."'                         \n";
        if ($this->getDado('stPlaca') != null)
            $stSql .="    AND v.placa = '".$this->getDado("stPlaca")."'                             \n";
        if ($this->getDado('inCodMarca') != null)
            $stSql .="    AND v.cod_marca = ".$this->getDado("inCodMarca")."                        \n";
        if ($this->getDado('inCodModelo') != null)
            $stSql .="    AND v.cod_modelo = ".$this->getDado("inCodModelo")."                      \n";
        if ($this->getDado('inCodTipoCombustivel') != null)
            $stSql .="    AND ci.cod_item = ".$this->getDado("inCodTipoCombustivel")."              \n";
        if ($this->getDado('inCodTipoVeiculo') != null)
            $stSql .="    AND tv.cod_tipo = ".$this->getDado("inCodTipoVeiculo")."                   \n";
        $stSql .="GROUP BY                                                                           \n";
        $stSql .="    ci.descricao                                                                   \n";
        $stSql .="    ,v.cod_veiculo                                                                 \n";
        $stSql .="    ,v.placa                                                                       \n";
        $stSql .="    ,mo.nom_modelo                                                                 \n";
        $stSql .="    ,ma.nom_marca                                                                  \n";
        $stSql .="    ,v.cilindrada                                                                  \n";
        $stSql .="    ,um.simbolo                                                                    \n";
        $stSql .="ORDER BY                                                                           \n";
        if ($this->getDado("inCodOrdenacao") == 2) {
            $stSql .="  ma.nom_marca                 \n";
            $stSql .=" ,mo.nom_modelo,               \n";
        }
        $stSql .=" v.placa                           \n";
        $stSql .=" ,ci.descricao                     \n";

        return $stSql;

    }

    public function recuperaVeiculoSintetico(&$rsRecordSet, $stFiltro="", $stOrder="", $boTransacao="")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql = $this->montaRecuperaVeiculoSintetico().$stFiltro.$stOrder;
        $this->stDebug = $stSql;

        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaVeiculoSintetico()
    {
        $stSql = "
           SELECT  veiculo.cod_veiculo
                ,  veiculo.cod_marca
                ,  marca.nom_marca
                ,  veiculo.cod_modelo
                ,  modelo.nom_modelo
                ,  veiculo.prefixo
                ,  veiculo.placa
                ,  CASE WHEN TRIM(veiculo.placa) <> '' THEN
                     SUBSTR(veiculo.placa,1,3) || '-' || SUBSTR(veiculo.placa,4,4)
                   END as placa_masc

                , SUBSTR(veiculo.placa,1,3) || '-' || SUBSTR(veiculo.placa,4,4) AS placa_masc
                ,  tipo_veiculo.nom_tipo AS tipo_veiculo
                ,  veiculo.cod_categoria
                ,  sw_categoria_habilitacao.nom_categoria

            FROM  frota.veiculo

      INNER JOIN  frota.marca
              ON  marca.cod_marca = veiculo.cod_marca

      INNER JOIN  frota.modelo
              ON  modelo.cod_modelo = veiculo.cod_modelo
             AND  modelo.cod_marca = veiculo.cod_marca

      INNER JOIN  frota.tipo_veiculo
              ON  tipo_veiculo.cod_tipo = veiculo.cod_tipo_veiculo

      INNER JOIN  sw_categoria_habilitacao
              ON  sw_categoria_habilitacao.cod_categoria = veiculo.cod_categoria

           WHERE  1=1 ";

        if ($this->getDado('cod_veiculo')) {
            $stSql .= " AND  veiculo.cod_veiculo = ".$this->getDado('cod_veiculo');
        }

        if ($this->getDado('prefixo')) {
            $stSql .= " AND  veiculo.prefixo = '".$this->getDado('prefixo')."'";
        }

        if ($this->getDado('placa')) {
            $stSql .= " AND  SUBSTR(veiculo.placa,1,3) || '-' || SUBSTR(veiculo.placa,4,4) ILIKE '%".$this->getDado('placa')."%'";
        }

        return $stSql;
    }

    public function recuperaVeiculoAnalitico(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        $stSql = isset($stSql) ? $stSql : " ";
        $this->stDebug = $stSql;

        return $this->executaRecupera("montaRecuperaVeiculoAnalitico",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function montaRecuperaVeiculoAnalitico()
    {
        $stSql = "
            SELECT veiculo.cod_veiculo
                 , veiculo.cod_marca
                 , marca.nom_marca
                 , veiculo.cod_modelo
                 , modelo.nom_modelo
                 , veiculo.cod_tipo_veiculo
                 , tipo_veiculo.nom_tipo
                 , veiculo.cod_categoria
                 , veiculo.prefixo
                 , veiculo.chassi
                 , TO_CHAR(veiculo.dt_aquisicao,'dd/mm/yyyy') AS dt_aquisicao
                 , veiculo.km_inicial
                 , veiculo.num_certificado
                 , veiculo.placa
                 , CASE WHEN ( veiculo.placa = '' )
                        THEN null
                        ELSE SUBSTR(veiculo.placa,1,3) || '-' || SUBSTR(veiculo.placa,4,4)
                   END AS placa_masc
                 , veiculo.ano_fabricacao
                 , veiculo.ano_modelo
                 , veiculo.categoria
                 , veiculo.cor
                 , veiculo.capacidade
                 , veiculo.potencia
                 , veiculo.cilindrada
                 , veiculo.num_passageiro
                 , veiculo.capacidade_tanque
                 , veiculo_propriedade.proprio
                 , propriedade.cod_propriedade
                 , propriedade.nom_propriedade
                 , CASE WHEN ( terceiros_historico.cod_orgao IS NOT NULL )
                        THEN terceiros_historico.cod_orgao || '.' || terceiros_historico.cod_local
                        ELSE ''
                   END AS localizacao
                 , veiculo_terceiros_responsavel.cod_responsavel
                 , veiculo_terceiros_responsavel.nom_responsavel
                 , veiculo_terceiros_responsavel.dt_inicio
                 , veiculo_uniorcam.exercicio as exercicio_entidade
                 , veiculo_uniorcam.cod_entidade 
                 , veiculo_uniorcam.num_orgao
                 , veiculo_uniorcam.num_unidade
              FROM frota.veiculo
        INNER JOIN frota.marca
                ON marca.cod_marca = veiculo.cod_marca
        INNER JOIN frota.modelo
                ON modelo.cod_modelo = veiculo.cod_modelo
               AND modelo.cod_marca = veiculo.cod_marca
        INNER JOIN frota.tipo_veiculo
                ON tipo_veiculo.cod_tipo = veiculo.cod_tipo_veiculo
         LEFT JOIN ( SELECT veiculo_propriedade.cod_veiculo
                          , veiculo_propriedade.timestamp
                          , veiculo_propriedade.proprio
                       FROM frota.veiculo_propriedade
                 INNER JOIN ( SELECT cod_veiculo
                                   , MAX(timestamp) AS timestamp
                                FROM frota.veiculo_propriedade
                            GROUP BY cod_veiculo
                            ) AS veiculo_propriedade_max
                         ON veiculo_propriedade_max.cod_veiculo = veiculo_propriedade.cod_veiculo
                        AND veiculo_propriedade_max.timestamp = veiculo_propriedade.timestamp
                   ) AS veiculo_propriedade
                ON veiculo_propriedade.cod_veiculo = veiculo.cod_veiculo
         LEFT JOIN ( SELECT CASE WHEN (terceiros.cod_veiculo IS NULL)
                                 THEN proprio.cod_veiculo
                                 ELSE terceiros.cod_veiculo
                            END AS cod_veiculo
                          , CASE WHEN (terceiros.timestamp IS NULL)
                                 THEN proprio.timestamp
                                 ELSE terceiros.timestamp
                            END AS timestamp
                          , CASE WHEN ( terceiros.cod_proprietario IS NULL )
                                 THEN proprio.cod_bem
                                 ELSE terceiros.cod_proprietario
                            END AS cod_propriedade
                          , CASE WHEN ( terceiros_cgm.nom_cgm IS NULL )
                                 THEN bem.descricao
                                 ELSE terceiros_cgm.nom_cgm
                            END AS nom_propriedade
                       FROM frota.veiculo_propriedade
                  LEFT JOIN frota.terceiros
                         ON terceiros.cod_veiculo = veiculo_propriedade.cod_veiculo
                        AND terceiros.timestamp = veiculo_propriedade.timestamp
                  LEFT JOIN sw_cgm AS terceiros_cgm
                         ON terceiros_cgm.numcgm = terceiros.cod_proprietario
                  LEFT JOIN frota.proprio
                         ON proprio.cod_veiculo = veiculo_propriedade.cod_veiculo
                        AND proprio.timestamp = veiculo_propriedade.timestamp
                  LEFT JOIN patrimonio.bem
                         ON bem.cod_bem = proprio.cod_bem
                  ) AS propriedade
               ON propriedade.cod_veiculo = veiculo_propriedade.cod_veiculo
              AND propriedade.timestamp = veiculo_propriedade.timestamp
        LEFT JOIN frota.terceiros_historico
               ON terceiros_historico.cod_veiculo = propriedade.cod_veiculo
              AND terceiros_historico.timestamp = propriedade.timestamp
        LEFT JOIN ( SELECT veiculo_terceiros_responsavel.cod_veiculo
                         , veiculo_terceiros_responsavel.numcgm AS cod_responsavel
                         , responsavel.nom_cgm AS nom_responsavel
                         , TO_CHAR(veiculo_terceiros_responsavel.dt_inicio,'dd/mm/yyyy') AS dt_inicio
                      FROM frota.veiculo_terceiros_responsavel
                INNER JOIN ( SELECT cod_veiculo
                                  , MAX(timestamp) AS timestamp
                               FROM frota.veiculo_terceiros_responsavel
                           GROUP BY cod_veiculo
                           ) AS veiculo_terceiros_responsavel_max
                        ON veiculo_terceiros_responsavel_max.cod_veiculo = veiculo_terceiros_responsavel.cod_veiculo
                       AND veiculo_terceiros_responsavel_max.timestamp = veiculo_terceiros_responsavel.timestamp
                INNER JOIN sw_cgm AS responsavel
                        ON responsavel.numcgm = veiculo_terceiros_responsavel.numcgm
                  ) AS veiculo_terceiros_responsavel
               ON veiculo_terceiros_responsavel.cod_veiculo = veiculo.cod_veiculo
     LEFT JOIN patrimonio.veiculo_uniorcam
              ON veiculo_uniorcam.cod_veiculo = veiculo.cod_veiculo
             WHERE ";
        if ( $this->getDado('cod_veiculo') ) {
            $stSql .= " veiculo.cod_veiculo = ".$this->getDado('cod_veiculo')." AND   ";
        }

        return substr($stSql,0,-6);
    }

    public function recuperaVeiculoConsulta(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
         return $this->executaRecupera("montaRecuperaVeiculoConsulta",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }
    public function montaRecuperaVeiculoConsulta()
    {
        $stSql = "
            SELECT veiculo.cod_veiculo
                 , marca.cod_marca
                 , marca.nom_marca
                 , modelo.cod_modelo
                 , modelo.nom_modelo
                 , tipo_veiculo.nom_tipo
                 , veiculo.prefixo
                 , veiculo.chassi
                 , veiculo.km_inicial
                 , veiculo.num_certificado
                 , SUBSTR(veiculo.placa,1,3) || '-' || SUBSTR(veiculo.placa,4,4) AS placa
                 , veiculo.ano_fabricacao
                 , veiculo.ano_modelo
                 , veiculo.categoria
                 , veiculo.cor
                 , veiculo.capacidade
                 , veiculo.potencia
                 , veiculo.cilindrada
                 , TO_CHAR(veiculo.dt_aquisicao,'dd/mm/yyyy') AS dt_aquisicao
                 , sw_categoria_habilitacao.nom_categoria
                 , orgao.cod_orgao
                 , orgao_descricao.descricao
                 , local.cod_local
                 , local.descricao as local_descricao
                 , TO_CHAR(veiculo_baixado.dt_baixa,'dd/mm/yyyy') AS dt_baixa
                 , veiculo_baixado.motivo
                 , propriedade.num_responsavel
                 , propriedade.nom_responsavel
                 , propriedade.num_proprietario
                 , propriedade.nom_proprietario
                 , propriedade.cod_bem
                 , propriedade.nom_bem
                 , bem_comprado.exercicio
                 , bem_comprado.cod_entidade
                 , entidade_cgm.nom_cgm AS nom_entidade
                 , bem_comprado.cod_empenho
                 , empenho_beneficiario.nom_cgm AS nom_empenho
                 , bem_comprado.nota_fiscal
              FROM frota.veiculo
        INNER JOIN frota.marca
                ON marca.cod_marca = veiculo.cod_marca
        INNER JOIN frota.modelo
                ON modelo.cod_marca = veiculo.cod_marca
               AND modelo.cod_modelo = veiculo.cod_modelo
        INNER JOIN frota.tipo_veiculo
                ON tipo_veiculo.cod_tipo = veiculo.cod_tipo_veiculo
        INNER JOIN sw_categoria_habilitacao
                ON sw_categoria_habilitacao.cod_categoria = veiculo.cod_categoria
        --recupera o historico do veiculo/bem
         LEFT JOIN ( SELECT veiculo.cod_veiculo
                          , CASE WHEN (terceiros_historico.cod_orgao IS NULL)
                                 THEN historico_bem.cod_orgao
                                 ELSE terceiros_historico.cod_orgao
                            END AS cod_orgao
                          , CASE WHEN (terceiros_historico.cod_local IS NULL)
                                 THEN historico_bem.cod_local
                                 ELSE terceiros_historico.cod_local
                            END AS cod_local
                          , CASE WHEN (veiculo_terceiros_responsavel.num_responsavel IS NULL)
                                 THEN bem_responsavel.num_responsavel
                                 ELSE veiculo_terceiros_responsavel.num_responsavel
                            END AS num_responsavel
                          , CASE WHEN (veiculo_terceiros_responsavel.nom_responsavel IS NULL)
                                 THEN bem_responsavel.nom_responsavel
                                 ELSE veiculo_terceiros_responsavel.nom_responsavel
                            END AS nom_responsavel
                          , CASE WHEN (proprietario.numcgm IS NULL)
                                 THEN null
                                 ELSE proprietario.numcgm
                            END AS num_proprietario
                          , CASE WHEN (proprietario.numcgm IS NULL)
                                 THEN null
                                 ELSE proprietario.nom_cgm
                            END AS nom_proprietario
                          , bem.cod_bem
                          , bem.descricao AS nom_bem
                       FROM frota.veiculo
                 INNER JOIN ( SELECT veiculo_propriedade.cod_veiculo
                                   , MAX(veiculo_propriedade.timestamp) AS timestamp
                                FROM frota.veiculo_propriedade
                            GROUP BY cod_veiculo
                            ) AS veiculo_propriedade_max
                         ON veiculo_propriedade_max.cod_veiculo = veiculo.cod_veiculo
                  LEFT JOIN frota.terceiros
                         ON terceiros.cod_veiculo = veiculo.cod_veiculo
                        AND terceiros.timestamp = veiculo_propriedade_max.timestamp
                  LEFT JOIN sw_cgm AS proprietario
                         ON proprietario.numcgm = terceiros.cod_proprietario
                  LEFT JOIN frota.terceiros_historico
                         ON terceiros_historico.cod_veiculo = veiculo.cod_veiculo
                        AND terceiros_historico.timestamp = veiculo_propriedade_max.timestamp
                  LEFT JOIN frota.proprio
                         ON proprio.cod_veiculo = veiculo.cod_veiculo
                        AND proprio.timestamp = veiculo_propriedade_max.timestamp
                  LEFT JOIN ( SELECT historico_bem.cod_bem
                                   , historico_bem.cod_orgao
                                   , historico_bem.cod_local
                                FROM patrimonio.historico_bem
                          INNER JOIN ( SELECT cod_bem
                                            , MAX(timestamp) AS timestamp
                                         FROM patrimonio.historico_bem
                                     GROUP BY cod_bem
                                     ) as historico_bem_max
                                  ON historico_bem_max.cod_bem = historico_bem.cod_bem
                                 AND historico_bem_max.timestamp = historico_bem.timestamp
                             ) AS historico_bem
                         ON historico_bem.cod_bem = proprio.cod_bem
                  LEFT JOIN ( SELECT veiculo_terceiros_responsavel.cod_veiculo
                                   , veiculo_terceiros_responsavel.numcgm AS num_responsavel
                                   , sw_cgm.nom_cgm AS nom_responsavel
                                   , TO_CHAR(veiculo_terceiros_responsavel.dt_inicio,'dd/mm/yyyy') AS dt_inicio
                                FROM frota.veiculo_terceiros_responsavel
                          INNER JOIN ( SELECT cod_veiculo
                                            , MAX(timestamp) AS timestamp
                                         FROM frota.veiculo_terceiros_responsavel
                                     GROUP BY cod_veiculo
                                      ) AS veiculo_terceiros_responsavel_max
                                   ON veiculo_terceiros_responsavel_max.cod_veiculo = veiculo_terceiros_responsavel.cod_veiculo
                                  AND veiculo_terceiros_responsavel_max.timestamp = veiculo_terceiros_responsavel.timestamp
                           INNER JOIN sw_cgm
                                   ON sw_cgm.numcgm = veiculo_terceiros_responsavel.numcgm
                             ) AS veiculo_terceiros_responsavel
                          ON veiculo_terceiros_responsavel.cod_veiculo = veiculo_propriedade_max.cod_veiculo
                   LEFT JOIN ( SELECT bem_responsavel.cod_bem
                                    , bem_responsavel.numcgm AS num_responsavel
                                    , sw_cgm.nom_cgm AS nom_responsavel
                                    , TO_CHAR( bem_responsavel.dt_inicio,'dd/mm/yyyy') AS dt_inicio
                                 FROM patrimonio.bem_responsavel
                           INNER JOIN ( SELECT cod_bem
                                             , MAX( timestamp ) AS timestamp
                                          FROM patrimonio.bem_responsavel
                                      GROUP BY cod_bem
                                      ) as bem_responsavel_max
                                   ON bem_responsavel_max.cod_bem = bem_responsavel.cod_bem
                                  AND bem_responsavel_max.timestamp = bem_responsavel.timestamp
                           INNER JOIN sw_cgm
                                   ON sw_cgm.numcgm = bem_responsavel.numcgm
                             ) AS bem_responsavel
                          ON bem_responsavel.cod_bem = proprio.cod_bem
                   LEFT JOIN patrimonio.bem
                          ON bem.cod_bem = proprio.cod_bem
                   ) AS propriedade
                ON propriedade.cod_veiculo = veiculo.cod_veiculo

        --recupera os dados financeiros do veiculo
         LEFT JOIN patrimonio.bem_comprado
                ON bem_comprado.cod_bem = propriedade.cod_bem

         LEFT JOIN empenho.empenho
                ON empenho.cod_empenho = bem_comprado.cod_empenho
               AND empenho.exercicio = bem_comprado.exercicio
               AND empenho.cod_entidade = bem_comprado.cod_entidade
         LEFT JOIN empenho.pre_empenho
                ON pre_empenho.cod_pre_empenho = empenho.cod_pre_empenho
               AND pre_empenho.exercicio = empenho.exercicio
         LEFT JOIN sw_cgm AS empenho_beneficiario
                ON empenho_beneficiario.numcgm = pre_empenho.cgm_beneficiario
         LEFT JOIN orcamento.entidade
                ON entidade.cod_entidade = bem_comprado.cod_entidade
               AND entidade.exercicio = bem_comprado.exercicio
         LEFT JOIN sw_cgm AS entidade_cgm
                ON entidade_cgm.numcgm = entidade.numcgm

         LEFT JOIN organograma.orgao
                ON orgao.cod_orgao = propriedade.cod_orgao
        LEFT JOIN organograma.orgao_descricao
                ON orgao.cod_orgao = propriedade.cod_orgao
         LEFT JOIN organograma.local
                ON local.cod_local = propriedade.cod_local
        --verifica se o veiculo esta baixado
         LEFT JOIN frota.veiculo_baixado
                ON veiculo_baixado.cod_veiculo = veiculo.cod_veiculo
             WHERE veiculo.cod_veiculo = ".$this->getDado('cod_veiculo')."
        ";

        return $stSql;

    }

}
