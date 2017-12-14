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
        * Classe de mapeamento da tabela EMPENHO.AUTORIZACAO_EMPENHO
        * Data de Criação: 30/11/2004

        * @author Analista: Jorge B. Ribarr
        * @author Desenvolvedor: Eduardo Martins

        * @package URBEM
        * @subpackage Mapeamento

        $Revision: 30668 $
        $Name$
        $Author: lbbarreiro $
        $Date: 2008-01-09 11:50:24 -0200 (Qua, 09 Jan 2008) $

        * Casos de uso: uc-02.03.02
                        uc-02.03.15
                        uc-02.01.08
    */

    /*
    $Log$
    Revision 1.14  2007/09/06 20:56:06  luciano
    Ticket#9094#

    Revision 1.13  2006/10/19 19:25:44  larocca
    Bug #7245#

    Revision 1.12  2006/07/11 20:27:52  eduardo
    Bug #6531#

    Revision 1.11  2006/07/10 17:26:32  cleisson
    Trocada função not in por exists para otimizar consulta

    Revision 1.10  2006/07/05 20:46:56  cleisson
    Adicionada tag Log aos arquivos

    */

    include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
    include_once ( CLA_PERSISTENTE );

    /**
      * Efetua conexão com a tabela  EMPENHO.AUTORIZACAO_EMPENHO
      * Data de Criação: 30/11/2004

      * @author Analista: Jorge B. Ribarr
      * @author Desenvolvedor: Eduardo Martins

      * @package URBEM
      * @subpackage Mapeamento
    */
    class TEmpenhoAutorizacaoEmpenho extends Persistente
    {
    /**
        * Método Construtor
        * @access Private
    */
    public function TEmpenhoAutorizacaoEmpenho()
    {
        parent::Persistente();
        $this->setTabela('empenho.autorizacao_empenho');

        $this->setCampoCod('cod_autorizacao');
        $this->setComplementoChave('exercicio, cod_entidade');

        $this->AddCampo('cod_pre_empenho','integer',true,'' ,false ,true );
        $this->AddCampo('cod_autorizacao','integer',true,'' ,true  ,false);
        $this->AddCampo('exercicio'      ,'char'   ,true,'4',true  ,true );
        $this->AddCampo('cod_entidade'   ,'integer',true,'' ,true  ,true );
        $this->AddCampo('dt_autorizacao' ,'date'   ,false,'' ,false ,false);
        $this->AddCampo('num_orgao'      ,'integer',true,'' ,false ,true);
        $this->AddCampo('num_unidade'    ,'integer',true,'' ,false ,true);
        $this->AddCampo('hora','time',false,'',false,false);
        $this->AddCampo('cod_categoria','integer',true,'',false,true);

    }

    /**
        * Monta a cláusula SQL
        * Cada tipo de campo recebe aqui um tratamento diferenciado em seu retorno.
        * @access Public
        * @return String String contendo o SQL
    */
    public function montaRecuperaRelacionamento()
    {
        $stSql  = "SELECT                                                                 \n";
        $stSql .= "    tabela.*,                                                          \n";
        $stSql .= "    CD.cod_estrutural  AS cod_estrutural_conta                         \n";
        $stSql .= "FROM (                                                                 \n";
        $stSql .= "    SELECT                                                             \n";
        $stSql .= "            AE.cod_autorizacao,                                        \n";
        $stSql .= "            TO_CHAR(AE.dt_autorizacao,'dd/mm/yyyy') AS dt_autorizacao, \n";
        $stSql .= "            PD.cod_despesa,                                            \n";
        $stSql .= "            D.cod_conta,                                               \n";
        $stSql .= "            CD.cod_estrutural AS cod_estrutural_rubrica,               \n";
        $stSql .= "            PE.descricao,                                              \n";
        $stSql .= "            PE.exercicio,                                              \n";
        $stSql .= "            PE.cod_pre_empenho,                                        \n";
        $stSql .= "            PE.cgm_beneficiario as credor,                             \n";
        $stSql .= "            AE.cod_entidade,                                           \n";
        $stSql .= "            AE.num_orgao,                                              \n";
        $stSql .= "            AE.num_unidade,                                            \n";
        $stSql .= "            AR.cod_reserva,                                            \n";
        $stSql .= "             C.nom_cgm as nom_fornecedor,                              \n";
        $stSql .= "        CASE WHEN O.anulada IS NOT NULL                                \n";
        $stSql .= "        THEN O.anulada                                                 \n";
        $stSql .= "        ELSE 'f'                                                       \n";
        $stSql .= "        END AS anulada                                                 \n";
        $stSql .= "    FROM                                                               \n";
        $stSql .= "            empenho.autorizacao_empenho AS AE                          \n";
        $stSql .= "     LEFT JOIN                                                         \n";
        $stSql .= "            empenho.autorizacao_reserva AS AR                          \n";
        $stSql .= "            ON (                                                       \n";
        $stSql .= "                 AR.exercicio       = AE.exercicio       AND           \n";
        $stSql .= "                 AR.cod_entidade    = AE.cod_entidade    AND           \n";
        $stSql .= "                 AR.cod_autorizacao = AE.cod_autorizacao               \n";
        $stSql .= "            )                                                          \n";
        $stSql .= "     LEFT JOIN                                                         \n";
        $stSql .= "          empenho.autorizacao_anulada AS AA                          \n";
        $stSql .= "            ON (                                                       \n";
        $stSql .= "                AA.cod_autorizacao = AE.cod_autorizacao AND            \n";
        $stSql .= "                AA.exercicio       = AE.exercicio       AND            \n";
        $stSql .= "                AA.cod_entidade    = AE.cod_entidade       )           \n";
        $stSql .= "     LEFT JOIN                                                         \n";
        $stSql .= "            orcamento.reserva           AS  O                          \n";
        $stSql .= "            ON (                                                       \n";
        $stSql .= "                 O.exercicio   = AR.exercicio   AND                    \n";
        $stSql .= "                 O.cod_reserva = AR.cod_reserva                        \n";
        $stSql .= "            ),                                                         \n";
        $stSql .= "            sw_cgm                         AS  C,                      \n";
        $stSql .= "            empenho.pre_empenho         AS PE                          \n";
        $stSql .= "     LEFT JOIN                                                         \n";
        $stSql .= "            empenho.pre_empenho_despesa AS PD                          \n";
        $stSql .= "            ON (                                                       \n";
        $stSql .= "                 PD.cod_pre_empenho = PE.cod_pre_empenho AND           \n";
        $stSql .= "                 PD.exercicio       = PE.exercicio                     \n";
        $stSql .= "                )                                                      \n";
        $stSql .= "     LEFT JOIN                                                         \n";
        $stSql .= "            orcamento.conta_despesa     AS CD                          \n";
        $stSql .= "             ON (                                                      \n";
        $stSql .= "                 CD.exercicio = PD.exercicio  AND                      \n";
        $stSql .= "                 CD.cod_conta = PD.cod_conta                           \n";
        $stSql .= "             )                                                         \n";
        $stSql .= "     LEFT JOIN                                                         \n";
        $stSql .= "            orcamento.despesa         AS D                             \n";
        $stSql .= "            ON (                                                       \n";
        $stSql .= "               D.exercicio   = PD.exercicio   AND                      \n";
        $stSql .= "               D.cod_despesa = PD.cod_despesa                          \n";
        $stSql .= "            )                                                          \n";
        $stSql .= "    WHERE                                                              \n";
        $stSql .= "            AE.cod_pre_empenho = PE.cod_pre_empenho   AND              \n";
        $stSql .= "            AE.exercicio       = PE.exercicio         AND              \n";
        $stSql .= "             C.numcgm          = PE.cgm_beneficiario  AND              \n";
        $stSql .= "            AA.cod_autorizacao IS NULL                                 \n";
        $stSql .= "        AND not exists ( select * from empenho.empenho_autorizacao as ea where AE.cod_autorizacao=ea.cod_autorizacao and AE.cod_entidade=ea.cod_entidade and AE.exercicio=ea.exercicio and  ea.cod_entidade = ae.cod_entidade AND     \n";
        $stSql .= "                ea.exercicio =  '".$this->getDado("exercicio")."'      \n";
    /*    $stSql .= "            AND  ea.cod_empenho||ea.cod_entidade||ea.exercicio not in (\n";
        $stSql .= "            select                                                     \n";
        $stSql .= "                eea.cod_empenho||eea.cod_entidade||eea.exercicio       \n";
        $stSql .= "            from                                                       \n";
        $stSql .= "                empenho.empenho_anulado as eea                         \n";
        $stSql .= "            )                                                          \n";*/
        $stSql .= "        )                                                              \n";
        $stSql .= ") AS tabela                                                            \n";
        $stSql .= "LEFT JOIN                                                              \n";
        $stSql .= "    orcamento.conta_despesa AS CD                                      \n";
        $stSql .= "    ON(                                                                \n";
        $stSql .= "        CD.exercicio = tabela.exercicio  AND                           \n";
        $stSql .= "        CD.cod_conta = tabela.cod_conta                                \n";
        $stSql .= "    )                                                                  \n";
        $stSql .= "LEFT JOIN                                                              \n";
        $stSql .= "    orcamento.despesa AS D                                             \n";
        $stSql .= "    ON (                                                               \n";
        $stSql .= "        D.cod_despesa = tabela.cod_despesa AND                         \n";
        $stSql .= "        D.exercicio   = tabela.exercicio                               \n";
        $stSql .= "    )                                                                  \n";
        $stSql .= "WHERE                                                                  \n";
        $stSql .= "       tabela.num_orgao::varchar||tabela.num_unidade::varchar                            \n";
        $stSql .= "       IN (                                                            \n";
        $stSql .= "            SELECT                                                     \n";
        $stSql .= "                  num_orgao::varchar||num_unidade::varchar                               \n";
        $stSql .= "            FROM                                                       \n";
        $stSql .= "                 empenho.permissao_autorizacao                         \n";
        $stSql .= "            WHERE                                                      \n";
        $stSql .= "                 numcgm    = ".$this->getDado("numcgm")."     AND      \n";
        $stSql .= "                 exercicio =  '".$this->getDado("exercicio")."'        \n";
        $stSql .= "       )                                                               \n";

        return $stSql;
    }
    
    public function recuperaItemMaterial(&$rsRecordSet, $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stCondicao  = " where     pe.cod_pre_empenho = " . $this->getDado( "cod_pre_empenho" ) . "\n ";
        $stCondicao .= "       and pe.exercicio       = '" . $this->getDado( "exercicio" ) . "'\n";

        $stSql = $this->montaRecuperaItemMaterial().$stCondicao;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaItemMaterial()
    {
        $stSql  = "select itpec.*\n";
        $stSql .= "from empenho.pre_empenho as pe\n";
        $stSql .= "     join empenho.item_pre_empenho as itpe\n";
        $stSql .= "          on (     itpe.cod_pre_empenho = pe.cod_pre_empenho\n";
        $stSql .= "               and itpe.exercicio       = pe.exercicio\n";
        $stSql .= "             )\n";
        $stSql .= "     join empenho.item_pre_empenho_compra as itpec\n";
        $stSql .= "          on (     itpec.cod_pre_empenho = itpe.cod_pre_empenho\n";
        $stSql .= "               and itpec.exercicio       = itpe.exercicio\n";
        $stSql .= "               and itpec.num_item        = itpe.num_item\n";
        $stSql .= "              )\n";

        return $stSql;
    }

    /**
        * Monta a cláusula SQL
        * Cada tipo de campo recebe aqui um tratamento diferenciado em seu retorno.
        * @access Public
        * @return String String contendo o SQL
    */
    public function montaRecuperaRelacionamentoConsulta()
    {
        $stSql  = "SELECT                                                                 \n";
        $stSql .= "    tabela.*,                                                          \n";
        $stSql .= "    D.cod_recurso,                                                     \n";
        $stSql .= "    CD.cod_estrutural  AS cod_estrutural_conta                         \n";
        $stSql .= "FROM (                                                                 \n";
        $stSql .= "    SELECT                                                             \n";
        $stSql .= "            AE.cod_autorizacao,                                        \n";
        $stSql .= "            EA.cod_empenho,                                            \n";
        $stSql .= "            TO_CHAR(AE.dt_autorizacao,'dd/mm/yyyy') AS dt_autorizacao, \n";
        $stSql .= "            PD.cod_despesa,                                            \n";
            $stSql .= "            D.cod_conta,                                               \n";
        $stSql .= "            CD.cod_estrutural AS cod_estrutural_rubrica,               \n";
        $stSql .= "            PE.descricao,                                              \n";
        $stSql .= "            PE.exercicio,                                              \n";
        $stSql .= "            PE.cod_pre_empenho,                                        \n";
        $stSql .= "            PE.cgm_beneficiario as credor,                             \n";
        $stSql .= "            PE.cod_historico,                                          \n";
        $stSql .= "            AE.cod_entidade,                                           \n";
        $stSql .= "            AE.num_orgao,                                              \n";
        $stSql .= "            AE.num_unidade,                                            \n";
        $stSql .= "            AR.cod_reserva,                                            \n";
        $stSql .= "             C.nom_cgm as nom_fornecedor,                              \n";
        $stSql .= "             CASE WHEN AA.cod_autorizacao > 0 THEN                     \n";
        $stSql .= "                 'Anulada'                                             \n";
        $stSql .= "             ELSE                                                      \n";
        $stSql .= "                 CASE WHEN EA.cod_autorizacao > 0 THEN                 \n";
        $stSql .= "                     'Empenhada'                                       \n";
        $stSql .= "                 ELSE                                                  \n";
        $stSql .= "                     'Não Empenhada'                                   \n";
        $stSql .= "                 END                                                   \n";
        $stSql .= "             END as situacao,                                          \n";
        $stSql .= "        CASE WHEN O.anulada IS NOT NULL                                \n";
        $stSql .= "        THEN O.anulada                                                 \n";
        $stSql .= "        ELSE 'f'                                                       \n";
        $stSql .= "        END AS anulada,                                                \n";
        $stSql .= "        sum(IPE.vl_total) as vl_empenhado                              \n";
        $stSql .= "    FROM                                                               \n";
        $stSql .= "            empenho.autorizacao_empenho AS AE                      \n";
        $stSql .= "     LEFT JOIN                                                         \n";
        $stSql .= "            empenho.autorizacao_reserva AS AR                      \n";
        $stSql .= "            ON (                                                       \n";
        $stSql .= "                 AR.exercicio       = AE.exercicio       AND           \n";
        $stSql .= "                 AR.cod_entidade    = AE.cod_entidade    AND           \n";
        $stSql .= "                 AR.cod_autorizacao = AE.cod_autorizacao               \n";
        $stSql .= "            )                                                          \n";
        $stSql .= "     LEFT JOIN                                                         \n";
        $stSql .= "          empenho.autorizacao_anulada AS AA                        \n";
        $stSql .= "            ON (                                                       \n";
        $stSql .= "                AA.cod_autorizacao = AE.cod_autorizacao AND            \n";
        $stSql .= "                AA.exercicio       = AE.exercicio       AND            \n";
        $stSql .= "                AA.cod_entidade    = AE.cod_entidade       )           \n";
        $stSql .= "     LEFT JOIN                                                         \n";
        $stSql .= "            orcamento.reserva           AS  O                      \n";
        $stSql .= "            ON (                                                       \n";
        $stSql .= "                 O.exercicio   = AR.exercicio   AND                    \n";
        $stSql .= "                 O.cod_reserva = AR.cod_reserva                        \n";
        $stSql .= "            )                                                         \n";
        $stSql .= "     LEFT JOIN                                                         \n";
        $stSql .= "            empenho.empenho_autorizacao AS EA                      \n";
        $stSql .= "            ON (                                                       \n";
        $stSql .= "                 EA.exercicio       = AE.exercicio       AND           \n";
        $stSql .= "                 EA.cod_entidade    = AE.cod_entidade    AND           \n";
        $stSql .= "                 EA.cod_autorizacao = AE.cod_autorizacao               \n";
        $stSql .= "            ),                                                          \n";
        $stSql .= "            sw_cgm                         AS  C,                     \n";
        $stSql .= "            empenho.pre_empenho         AS PE                      \n";
        $stSql .= "     LEFT JOIN                                                         \n";
        $stSql .= "            empenho.pre_empenho_despesa AS PD                      \n";
        $stSql .= "             ON (                                                      \n";
        $stSql .= "                 PD.cod_pre_empenho = PE.cod_pre_empenho AND           \n";
        $stSql .= "                 PD.exercicio       = PE.exercicio                     \n";
            $stSql .= "                )                                                      \n";
        $stSql .= "     LEFT JOIN                                                         \n";
        $stSql .= "            empenho.item_pre_empenho AS IPE                        \n";
        $stSql .= "             ON (                                                      \n";
        $stSql .= "                 IPE.cod_pre_empenho = PE.cod_pre_empenho AND           \n";
        $stSql .= "                 IPE.exercicio       = PE.exercicio                     \n";
        $stSql .= "                )                                                      \n";
        $stSql .= "     LEFT JOIN                                                         \n";
        $stSql .= "            orcamento.conta_despesa     AS CD                      \n";
        $stSql .= "             ON (                                                      \n";
        $stSql .= "                 CD.exercicio = PD.exercicio  AND                      \n";
        $stSql .= "                 CD.cod_conta = PD.cod_conta                           \n";
        $stSql .= "             )                                                         \n";
        $stSql .= "     LEFT JOIN                                                         \n";
        $stSql .= "            orcamento.despesa         AS D                         \n";
        $stSql .= "            ON (                                                       \n";
        $stSql .= "               D.exercicio   = PD.exercicio   AND                      \n";
        $stSql .= "               D.cod_despesa = PD.cod_despesa                          \n";
        $stSql .= "            )                                                          \n";
        $stSql .= "    WHERE                                                              \n";
        $stSql .= "            AE.cod_pre_empenho = PE.cod_pre_empenho   AND              \n";
        $stSql .= "            AE.exercicio       = PE.exercicio         AND              \n";
        $stSql .= "             C.numcgm          = PE.cgm_beneficiario                   \n";
    /*    $stSql .= "            AA.cod_autorizacao IS NULL                              \n";
        $stSql .= "        AND AE.cod_autorizacao||AE.cod_entidade||AE.exercicio not in (     \n";
        $stSql .= "            select ea.cod_autorizacao||cod_entidade||ea.exercicio      \n";
        $stSql .= "            from                                                       \n";
        $stSql .= "                ". EMPENHO_EMPENHO_AUTORIZACAO ."   as ea              \n";
        $stSql .= "            where                                                      \n";
        $stSql .= "                ea.cod_entidade = ae.cod_entidade    AND               \n";
        $stSql .= "                ea.exercicio =  '".$this->getDado("exercicio")."'      \n";
        $stSql .= "            AND  ea.cod_empenho||ea.cod_entidade||ea.exercicio not in (\n";
        $stSql .= "            select                                                     \n";
        $stSql .= "                eea.cod_empenho||eea.cod_entidade||eea.exercicio       \n";
        $stSql .= "            from                                                       \n";
        $stSql .= "                empenho.empenho_anulado as eea                         \n";
        $stSql .= "            )                                                          \n";
        $stSql .= "        )                                                              \n";*/
        $stSql .= "     GROUP BY                                                          \n";
        $stSql .= "             AE.cod_autorizacao,                                       \n";
        $stSql .= "             EA.cod_empenho,                                            \n";
        $stSql .= "             TO_CHAR(AE.dt_autorizacao,'dd/mm/yyyy'),                  \n";
        $stSql .= "             PD.cod_despesa,                                           \n";
        $stSql .= "             D.cod_conta,                                              \n";
        $stSql .= "             CD.cod_estrutural,                                        \n";
        $stSql .= "             PE.descricao,                                             \n";
        $stSql .= "             PE.exercicio,                                             \n";
        $stSql .= "             PE.cod_pre_empenho,                                       \n";
        $stSql .= "             PE.cgm_beneficiario,                                      \n";
        $stSql .= "             PE.cod_historico,                                         \n";
        $stSql .= "             AE.cod_entidade,                                          \n";
        $stSql .= "             AE.num_orgao,                                             \n";
        $stSql .= "             AE.num_unidade,                                           \n";
        $stSql .= "             AR.cod_reserva,                                           \n";
        $stSql .= "             C.nom_cgm,                                                \n";
        $stSql .= "             situacao,                                                 \n";
        $stSql .= "             anulada                                                   \n";
        $stSql .= ") AS tabela                                                            \n";
        $stSql .= "LEFT JOIN                                                              \n";
        $stSql .= "    orcamento.conta_despesa AS CD                                  \n";
        $stSql .= "    ON(                                                                \n";
        $stSql .= "        CD.exercicio = tabela.exercicio  AND                           \n";
        $stSql .= "        CD.cod_conta = tabela.cod_conta                                \n";
        $stSql .= "    )                                                                  \n";
        $stSql .= "LEFT JOIN                                                              \n";
        $stSql .= "    orcamento.despesa AS D                                         \n";
        $stSql .= "    ON (                                                               \n";
        $stSql .= "        D.cod_despesa = tabela.cod_despesa AND                         \n";
        $stSql .= "        D.exercicio   = tabela.exercicio                               \n";
        $stSql .= "    )                                                                  \n";
        $stSql .= "LEFT JOIN                                                              \n";
        $stSql .= "    orcamento.recurso AS REC                                            \n";
        $stSql .= "    ON (                                                               \n";
        $stSql .= "        D.cod_recurso = rec.cod_recurso AND                         \n";
        $stSql .= "        D.exercicio   = rec.exercicio                               \n";
        $stSql .= "    )                                                                  \n";
        $stSql .= "WHERE                                                                  \n";
        $stSql .= "       tabela.num_orgao::varchar||tabela.num_unidade::varchar                            \n";
        $stSql .= "       IN (                                                            \n";
        $stSql .= "            SELECT                                                     \n";
        $stSql .= "                  num_orgao::varchar||num_unidade::varchar                               \n";
        $stSql .= "            FROM                                                       \n";
        $stSql .= "                 empenho.permissao_autorizacao                     \n";
        $stSql .= "            WHERE                                                      \n";
        $stSql .= "                 numcgm    = ".$this->getDado("numcgm")."     AND      \n";
        $stSql .= "                 exercicio =  '".$this->getDado("exercicio")."'        \n";
        $stSql .= "       )                                                               \n";

        return $stSql;
    }
    
     /**
        * Monta a cláusula SQL
        * Cada tipo de campo recebe aqui um tratamento diferenciado em seu retorno.
        * @access Public
        * @return String String contendo o SQL
    */
    public function montaRecuperaRelacionamentoConsultaCompraLicitacao()
    {
        $stSql = "
                   SELECT tabela.*,                                                          
                          D.cod_recurso,                                                     
                          CD.cod_estrutural  AS cod_estrutural_conta                         
                     FROM (                                                                 
                            SELECT AE.cod_autorizacao,                                        
                                   EA.cod_empenho,                                            
                                   TO_CHAR(AE.dt_autorizacao,'dd/mm/yyyy') AS dt_autorizacao, 
                                   PD.cod_despesa,                                            
                                   D.cod_conta,                                               
                                   CD.cod_estrutural AS cod_estrutural_rubrica,               
                                   PE.descricao,                                              
                                   PE.exercicio,                                              
                                   PE.cod_pre_empenho,                                        
                                   PE.cgm_beneficiario as credor,                             
                                   PE.cod_historico,                                          
                                   AE.cod_entidade,                                           
                                   AE.num_orgao,                                              
                                   AE.num_unidade,                                            
                                   AR.cod_reserva,                                            
                                   C.nom_cgm as nom_fornecedor,                              
                                   CASE WHEN AA.cod_autorizacao > 0
                                        THEN 'Anulada'                                             
                                    ELSE                                                      
                                        CASE WHEN EA.cod_autorizacao > 0
                                             THEN 'Empenhada'                                       
                                             ELSE 'Não Empenhada'                                   
                                        END                                                   
                                  END as situacao,                                          
                                  CASE WHEN O.anulada IS NOT NULL                                
                                       THEN O.anulada                                                 
                                       ELSE 'f'                                                       
                                  END AS anulada,
                                  sum(IPE.vl_total) as vl_empenhado
                                , compra_direta.cod_modalidade AS compra_cod_modalidade
                                , compra_direta.cod_compra_direta
                                , compra_modalidade.descricao AS compra_modalidade
                                , adjudicacao.cod_modalidade AS licitacao_cod_modalidade
                                , adjudicacao.cod_licitacao
                                , licitacao_modalidade.descricao AS licitacao_modalidade
                                  
                        FROM empenho.autorizacao_empenho AS AE                      
                   
                    LEFT JOIN empenho.autorizacao_reserva AS AR                      
                           ON AR.exercicio       = AE.exercicio
                          AND AR.cod_entidade    = AE.cod_entidade
                          AND AR.cod_autorizacao = AE.cod_autorizacao               
                                                                                        
                    LEFT JOIN empenho.autorizacao_anulada AS AA                        
                           ON AA.cod_autorizacao = AE.cod_autorizacao
                          AND AA.exercicio       = AE.exercicio
                          AND AA.cod_entidade    = AE.cod_entidade
                       
                    LEFT JOIN orcamento.reserva AS  O                      
                           ON O.exercicio   = AR.exercicio
                          AND O.cod_reserva = AR.cod_reserva                        
                              
                    LEFT JOIN empenho.empenho_autorizacao AS EA                      
                           ON EA.exercicio       = AE.exercicio
                          AND EA.cod_entidade    = AE.cod_entidade
                          AND EA.cod_autorizacao = AE.cod_autorizacao               
                              
                    INNER JOIN empenho.pre_empenho  AS PE
                            ON AE.cod_pre_empenho = PE.cod_pre_empenho
                           AND AE.exercicio       = PE.exercicio
                    
                    INNER JOIN sw_cgm AS  C
                            ON C.numcgm = PE.cgm_beneficiario
                           
                    LEFT JOIN empenho.pre_empenho_despesa AS PD                      
                           ON PD.cod_pre_empenho = PE.cod_pre_empenho
                          AND PD.exercicio       = PE.exercicio                     
                              
                    LEFT JOIN empenho.item_pre_empenho AS IPE                        
                           ON IPE.cod_pre_empenho = PE.cod_pre_empenho
                          AND IPE.exercicio       = PE.exercicio                     
                           
                    LEFT JOIN orcamento.conta_despesa AS CD                      
                           ON CD.exercicio = PD.exercicio
                          AND CD.cod_conta = PD.cod_conta                           
                              
                    LEFT JOIN orcamento.despesa AS D                         
                           ON D.exercicio   = PD.exercicio
                          AND D.cod_despesa = PD.cod_despesa  
                          
                    LEFT JOIN empenho.item_pre_empenho_julgamento
                           ON item_pre_empenho_julgamento.cod_pre_empenho  = IPE.cod_pre_empenho   
                          AND item_pre_empenho_julgamento.exercicio        = IPE.exercicio
                          AND item_pre_empenho_julgamento.num_item         = IPE.num_item
                    
                    LEFT JOIN compras.julgamento_item
                           ON julgamento_item.exercicio      = item_pre_empenho_julgamento.exercicio_julgamento
                          AND julgamento_item.cod_cotacao    = item_pre_empenho_julgamento.cod_cotacao 
                          AND julgamento_item.cod_item       = item_pre_empenho_julgamento.cod_item
                          AND julgamento_item.lote           = item_pre_empenho_julgamento.lote
                          AND julgamento_item.cgm_fornecedor = item_pre_empenho_julgamento.cgm_fornecedor
                    
                    LEFT JOIN compras.cotacao_item
                           ON cotacao_item.exercicio   = julgamento_item.exercicio
                          AND cotacao_item.cod_cotacao = julgamento_item.cod_cotacao
                          AND cotacao_item.lote        = julgamento_item.lote
                          AND cotacao_item.cod_item    = julgamento_item.cod_item
                    
                    LEFT JOIN compras.cotacao
                           ON cotacao.cod_cotacao = cotacao_item.cod_cotacao
                          AND cotacao.exercicio   = cotacao_item.exercicio
                    
                    LEFT JOIN compras.mapa_cotacao
                           ON mapa_cotacao.cod_cotacao       = cotacao.cod_cotacao
                          AND mapa_cotacao.exercicio_cotacao = cotacao.exercicio
                    
                    LEFT JOIN compras.mapa
                           ON mapa.cod_mapa  = mapa_cotacao.cod_mapa
                          AND mapa.exercicio = mapa_cotacao.exercicio_mapa
                    
                    LEFT JOIN compras.compra_direta
                           ON compra_direta.cod_mapa       = mapa.cod_mapa
                          AND compra_direta.exercicio_mapa = mapa.exercicio

                    LEFT JOIN compras.modalidade AS compra_modalidade
                           ON compra_modalidade.cod_modalidade = compra_direta.cod_modalidade

                    LEFT JOIN licitacao.adjudicacao
                           ON adjudicacao.exercicio_cotacao = cotacao_item.exercicio 
                          AND adjudicacao.cod_cotacao       = cotacao_item.cod_cotacao
                          AND adjudicacao.lote              = cotacao_item.lote
                          AND adjudicacao.cod_item          = cotacao_item.cod_item
                          
                    LEFT JOIN compras.modalidade AS licitacao_modalidade
                           ON licitacao_modalidade.cod_modalidade = adjudicacao.cod_modalidade
        
                    GROUP BY AE.cod_autorizacao,                                       
                             EA.cod_empenho,                            
                             TO_CHAR(AE.dt_autorizacao,'dd/mm/yyyy'),                  
                             PD.cod_despesa,                                           
                             D.cod_conta,                                              
                             CD.cod_estrutural,                                        
                             PE.descricao,                                             
                             PE.exercicio,                                             
                             PE.cod_pre_empenho,                                       
                             PE.cgm_beneficiario,                                      
                             PE.cod_historico,                                         
                             AE.cod_entidade,                                          
                             AE.num_orgao,                                             
                             AE.num_unidade,                                           
                             AR.cod_reserva,                                           
                             C.nom_cgm,                                                
                             situacao,                                                 
                             anulada
                            ,compra_direta.cod_modalidade
                            ,compra_direta.cod_compra_direta
                            ,adjudicacao.cod_modalidade
                            ,adjudicacao.cod_licitacao
                           , compra_modalidade.descricao 
                           , licitacao_modalidade.descricao 
                            
                    ) AS tabela

            LEFT JOIN orcamento.conta_despesa AS CD                                  
                   ON CD.exercicio = tabela.exercicio
                  AND CD.cod_conta = tabela.cod_conta                                
                
            LEFT JOIN orcamento.despesa AS D                                         
                   ON D.cod_despesa = tabela.cod_despesa
                  AND D.exercicio   = tabela.exercicio                               
                                                                             
            LEFT JOIN orcamento.recurso AS REC                                            
                   ON D.cod_recurso = rec.cod_recurso
                  AND D.exercicio   = rec.exercicio                               
                                                               
                WHERE tabela.num_orgao::varchar||tabela.num_unidade::varchar                            
                   IN ( SELECT num_orgao::varchar||num_unidade::varchar                               
                          FROM empenho.permissao_autorizacao                     
                         WHERE numcgm    = ".$this->getDado("numcgm")."
                           AND exercicio = '".$this->getDado("exercicio")."'        
                       )
        ";
                
        return $stSql;
    }
    
    public function recuperaRelacionamentoConsultaCompraLicitacao(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        if(trim($stOrdem))
            $stOrdem = (strpos($stOrdem,"ORDER BY") === false)?" ORDER BY $stOrdem":$stOrdem;
        $stSql = $this->montaRecuperaRelacionamentoConsultaCompraLicitacao().$stCondicao.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }
    
    public function montaRecuperaEmpenhoCompraLicitacao()
    {
        $stSql = " SELECT DISTINCT tabela.*
                        , CD.cod_estrutural  AS cod_estrutural_conta                         
                   FROM (                                                                 
                        SELECT  AE.cod_autorizacao,                                        
                                TO_CHAR(AE.dt_autorizacao,'dd/mm/yyyy') AS dt_autorizacao, 
                                PD.cod_despesa,                                            
                                D.cod_conta,                                               
                                CD.cod_estrutural AS cod_estrutural_rubrica,               
                                PE.descricao,                                              
                                PE.exercicio,                                              
                                PE.cod_pre_empenho,                                        
                                PE.cgm_beneficiario as credor,                             
                                AE.cod_entidade,                                           
                                AE.num_orgao,                                              
                                AE.num_unidade,                                            
                                AR.cod_reserva,                                            
                                C.nom_cgm as nom_fornecedor,                              
                            CASE WHEN O.anulada IS NOT NULL                                
                                 THEN O.anulada                                                 
                                 ELSE 'f'                                                       
                            END AS anulada,
                            compra_direta.cod_modalidade AS compra_cod_modalidade,
                            compra_direta.cod_compra_direta,
                            adjudicacao.cod_modalidade AS licitacao_cod_modalidade,
                            adjudicacao.cod_licitacao,
                            item_pre_empenho.cod_centro AS centro_custo
                            
                      FROM empenho.autorizacao_empenho AS AE
                      
                 LEFT JOIN empenho.autorizacao_reserva AS AR                          
                        ON AR.exercicio       = AE.exercicio
                       AND AR.cod_entidade    = AE.cod_entidade
                       AND AR.cod_autorizacao = AE.cod_autorizacao               
                        
                 LEFT JOIN empenho.autorizacao_anulada AS AA                          
                        ON AA.cod_autorizacao = AE.cod_autorizacao
                       AND AA.exercicio       = AE.exercicio
                       AND AA.cod_entidade    = AE.cod_entidade   
                        
                 LEFT JOIN orcamento.reserva AS  O                          
                        ON O.exercicio  = AR.exercicio
                      AND O.cod_reserva = AR.cod_reserva                        
                               
                INNER JOIN empenho.pre_empenho AS PE
                        ON AE.cod_pre_empenho = PE.cod_pre_empenho
                       AND AE.exercicio       = PE.exercicio
                      
               INNER JOIN sw_cgm AS  C
                       ON C.numcgm  = PE.cgm_beneficiario
    
                LEFT JOIN empenho.item_pre_empenho
                       ON item_pre_empenho.cod_pre_empenho = pe.cod_pre_empenho
                      AND item_pre_empenho.exercicio       = pe.exercicio
                      
                LEFT JOIN empenho.item_pre_empenho_julgamento
                       ON item_pre_empenho_julgamento.cod_pre_empenho  = item_pre_empenho.cod_pre_empenho   
                      AND item_pre_empenho_julgamento.exercicio        = item_pre_empenho.exercicio
                      AND item_pre_empenho_julgamento.num_item         = item_pre_empenho.num_item
      
               LEFT JOIN compras.julgamento_item
                      ON julgamento_item.exercicio      = item_pre_empenho_julgamento.exercicio_julgamento
                     AND julgamento_item.cod_cotacao    = item_pre_empenho_julgamento.cod_cotacao 
                     AND julgamento_item.cod_item       = item_pre_empenho_julgamento.cod_item
                     AND julgamento_item.lote           = item_pre_empenho_julgamento.lote
                     AND julgamento_item.cgm_fornecedor = item_pre_empenho_julgamento.cgm_fornecedor
      
               LEFT JOIN compras.cotacao_item
                      ON cotacao_item.exercicio   = julgamento_item.exercicio
                     AND cotacao_item.cod_cotacao = julgamento_item.cod_cotacao
                     AND cotacao_item.lote        = julgamento_item.lote
                     AND cotacao_item.cod_item    = julgamento_item.cod_item
               
               LEFT JOIN compras.cotacao
                      ON cotacao.cod_cotacao = cotacao_item.cod_cotacao
                     AND cotacao.exercicio   = cotacao_item.exercicio
               
               LEFT JOIN compras.mapa_cotacao
                      ON mapa_cotacao.cod_cotacao       = cotacao.cod_cotacao
                     AND mapa_cotacao.exercicio_cotacao = cotacao.exercicio
               
               LEFT JOIN compras.mapa
                      ON mapa.cod_mapa  = mapa_cotacao.cod_mapa
                     AND mapa.exercicio = mapa_cotacao.exercicio_mapa
               
               LEFT JOIN compras.compra_direta
                      ON compra_direta.cod_mapa       = mapa.cod_mapa
                     AND compra_direta.exercicio_mapa = mapa.exercicio
      
               LEFT JOIN licitacao.adjudicacao
                      ON adjudicacao.exercicio_cotacao = cotacao_item.exercicio 
                     AND adjudicacao.cod_cotacao       = cotacao_item.cod_cotacao
                     AND adjudicacao.lote              = cotacao_item.lote
                     AND adjudicacao.cod_item          = cotacao_item.cod_item 
                   
              LEFT JOIN empenho.pre_empenho_despesa AS PD                          
                     ON PD.cod_pre_empenho = PE.cod_pre_empenho
                    AND PD.exercicio       = PE.exercicio                     
                                                                               
              LEFT JOIN orcamento.conta_despesa AS CD                          
                     ON CD.exercicio = PD.exercicio
                    AND CD.cod_conta = PD.cod_conta                           
                                                                              
              LEFT JOIN orcamento.despesa AS D                             
                     ON D.exercicio   = PD.exercicio
                    AND D.cod_despesa = PD.cod_despesa                          
                                                                               
                  WHERE AA.cod_autorizacao IS NULL                                 
                    AND NOT EXISTS ( SELECT *
                                       FROM empenho.empenho_autorizacao AS ea
                                      WHERE AE.cod_autorizacao = ea.cod_autorizacao
                                        AND AE.cod_entidade    = ea.cod_entidade
                                        AND AE.exercicio       = ea.exercicio
                                        AND ea.cod_entidade    = ae.cod_entidade
                                        AND ea.exercicio       =  '".$this->getDado("exercicio")."'      
                                    )   
                   ) AS tabela
                   
        LEFT JOIN orcamento.conta_despesa AS CD                                      
               ON CD.exercicio = tabela.exercicio
              AND CD.cod_conta = tabela.cod_conta                                
                                                                         
        LEFT JOIN orcamento.despesa AS D                                             
               ON D.cod_despesa = tabela.cod_despesa
              AND D.exercicio   = tabela.exercicio  
        
            WHERE tabela.num_orgao::varchar||tabela.num_unidade::varchar                            
               IN ( SELECT num_orgao::varchar||num_unidade::varchar                               
                      FROM empenho.permissao_autorizacao                         
                     WHERE numcgm  = ".$this->getDado("numcgm")."
                       AND exercicio =  '".$this->getDado("exercicio")."'        
                  )  
        ";
        
        return $stSql;
    }
    
    public function recuperaEmpenhoCompraLicitacao(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        if(trim($stOrdem))
            $stOrdem = (strpos($stOrdem,"ORDER BY") === false)?" ORDER BY $stOrdem":$stOrdem;
        $stSql = $this->montaRecuperaEmpenhoCompraLicitacao().$stCondicao.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function recuperaRelacionamentoConsulta(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        if(trim($stOrdem))
            $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
        $stSql = $this->montaRecuperaRelacionamentoConsulta().$stCondicao.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function recuperaRelacionamentoTodos(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        if(trim($stOrdem))
            $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
        $stSql = $this->montaRecuperaRelacionamentoTodos().$stCondicao.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function recuperaRelacionamentoPorPreEmpenho(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        if(trim($stOrdem))
            $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
        $stSql = $this->montaRecuperaRelacionamentoPorPreEmpenho().$stCondicao.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    /**
        * Monta a cláusula SQL
        * Cada tipo de campo recebe aqui um tratamento diferenciado em seu retorno.
        * @access Public
        * @return String String contendo o SQL
    */
    public function montaRecuperaRelacionamentoPorPreEmpenho()
    {
        $stSql  = "SELECT                                                                 \n";
        $stSql .= "    ae.cod_autorizacao,                                                \n";
        $stSql .= "    ae.exercicio,                                                      \n";
        $stSql .= "    ae.cod_entidade,                                                   \n";
        $stSql .= "    ae.num_orgao,                                                      \n";
        $stSql .= "    ae.num_unidade,                                                    \n";
        $stSql .= "    ae.cod_pre_empenho,                                                \n";
        $stSql .= "    to_char(ae.dt_autorizacao,'dd/mm/yyyy') as dt_autorizacao,         \n";
        $stSql .= "    ar.cod_reserva,                                                    \n";
        $stSql .= "    CASE WHEN aa.cod_autorizacao > 0 THEN                              \n";
        $stSql .= "         't'                                                           \n";
        $stSql .= "    ELSE                                                               \n";
        $stSql .= "         'f'                                                           \n";
        $stSql .= "    END as anulada                                                     \n";
        $stSql .= "FROM                                                                   \n";
        $stSql .= "    empenho.autorizacao_empenho as ae                                  \n";
        $stSql .= "         LEFT JOIN empenho.autorizacao_anulada as aa ON (              \n";
        $stSql .= "             ae.cod_autorizacao  = aa.cod_autorizacao AND              \n";
        $stSql .= "             ae.exercicio  = aa.exercicio AND                          \n";
        $stSql .= "             ae.cod_entidade  = aa.cod_entidade                        \n";
        $stSql .= "         )                                                             \n";
        $stSql .= "         LEFT JOIN empenho.autorizacao_reserva as ar ON (              \n";
        $stSql .= "             ae.cod_autorizacao  = ar.cod_autorizacao AND              \n";
        $stSql .= "             ae.exercicio  = ar.exercicio AND                          \n";
        $stSql .= "             ae.cod_entidade  = ar.cod_entidade                        \n";
        $stSql .= "         )                                                             \n";

        return $stSql;
    }

    /**
        * Monta a cláusula SQL
        * Cada tipo de campo recebe aqui um tratamento diferenciado em seu retorno.
        * @access Public
        * @return String String contendo o SQL
    */
    public function montaRecuperaRelacionamentoTodos()
    {
        $stSql  = "SELECT                                                                 \n";
        $stSql .= "    tabela.*,                                                          \n";
        $stSql .= "    CD.cod_estrutural  AS cod_estrutural_conta                         \n";
        $stSql .= "FROM (                                                                 \n";
        $stSql .= "    SELECT                                                             \n";
        $stSql .= "            AE.cod_autorizacao,                                        \n";
        $stSql .= "            TO_CHAR(AE.dt_autorizacao,'dd/mm/yyyy') AS dt_autorizacao, \n";
        $stSql .= "            PD.cod_despesa,                                            \n";
        $stSql .= "            D.cod_conta,                                               \n";
        $stSql .= "            CD.cod_estrutural AS cod_estrutural_rubrica,               \n";
        $stSql .= "            PE.descricao,                                              \n";
        $stSql .= "            PE.exercicio,                                              \n";
        $stSql .= "            PE.cod_pre_empenho,                                        \n";
        $stSql .= "            PE.cgm_beneficiario as credor,                             \n";
        $stSql .= "            AE.cod_entidade,                                           \n";
        $stSql .= "            AE.num_orgao,                                              \n";
        $stSql .= "            AE.num_unidade,                                            \n";
        $stSql .= "            AR.cod_reserva,                                            \n";
        $stSql .= "             C.nom_cgm as nom_fornecedor,                              \n";
        $stSql .= "        CASE WHEN O.anulada IS NOT NULL                                \n";
        $stSql .= "        THEN O.anulada                                                 \n";
        $stSql .= "        ELSE 'f'                                                       \n";
        $stSql .= "        END AS anulada                                                 \n";
        $stSql .= "    FROM                                                               \n";
        $stSql .= "            empenho.autorizacao_empenho AS AE                      \n";
        $stSql .= "     LEFT JOIN                                                         \n";
        $stSql .= "            empenho.autorizacao_reserva AS AR                      \n";
        $stSql .= "            ON (                                                       \n";
        $stSql .= "                 AR.exercicio       = AE.exercicio       AND           \n";
        $stSql .= "                 AR.cod_entidade    = AE.cod_entidade    AND           \n";
        $stSql .= "                 AR.cod_autorizacao = AE.cod_autorizacao               \n";
        $stSql .= "            )                                                          \n";
        $stSql .= "     LEFT JOIN                                                         \n";
        $stSql .= "          empenho.autorizacao_anulada AS AA                        \n";
        $stSql .= "            ON (                                                       \n";
        $stSql .= "                AA.cod_autorizacao = AE.cod_autorizacao AND            \n";
        $stSql .= "                AA.exercicio       = AE.exercicio       AND            \n";
        $stSql .= "                AA.cod_entidade    = AE.cod_entidade       )           \n";
        $stSql .= "     LEFT JOIN                                                         \n";
        $stSql .= "            orcamento.reserva           AS  O                      \n";
        $stSql .= "            ON (                                                       \n";
        $stSql .= "                 O.exercicio   = AR.exercicio   AND                    \n";
        $stSql .= "                 O.cod_reserva = AR.cod_reserva                        \n";
        $stSql .= "            ),                                                         \n";
        $stSql .= "            sw_cgm                         AS  C,                     \n";
        $stSql .= "            empenho.pre_empenho         AS PE                      \n";
        $stSql .= "     LEFT JOIN                                                         \n";
        $stSql .= "            empenho.pre_empenho_despesa AS PD                      \n";
        $stSql .= "             ON (                                                      \n";
        $stSql .= "                 PD.cod_pre_empenho = PE.cod_pre_empenho AND           \n";
        $stSql .= "                 PD.exercicio       = PE.exercicio                     \n";
        $stSql .= "                )                                                      \n";
        $stSql .= "     LEFT JOIN                                                         \n";
        $stSql .= "            orcamento.conta_despesa     AS CD                      \n";
        $stSql .= "             ON (                                                      \n";
        $stSql .= "                 CD.exercicio = PD.exercicio  AND                      \n";
        $stSql .= "                 CD.cod_conta = PD.cod_conta                           \n";
        $stSql .= "             )                                                         \n";
        $stSql .= "     LEFT JOIN                                                         \n";
        $stSql .= "            orcamento.despesa         AS D                         \n";
        $stSql .= "            ON (                                                       \n";
        $stSql .= "               D.exercicio   = PD.exercicio   AND                      \n";
        $stSql .= "               D.cod_despesa = PD.cod_despesa                          \n";
        $stSql .= "            )                                                          \n";
        $stSql .= "    WHERE                                                              \n";
        $stSql .= "            AE.cod_pre_empenho = PE.cod_pre_empenho   AND              \n";
        $stSql .= "            AE.exercicio       = PE.exercicio         AND              \n";
        $stSql .= "             C.numcgm          = PE.cgm_beneficiario  AND              \n";
        $stSql .= "            AA.cod_autorizacao IS NULL                              \n";
        $stSql .= ") AS tabela                                                            \n";
        $stSql .= "LEFT JOIN                                                              \n";
        $stSql .= "    orcamento.conta_despesa AS CD                                  \n";
        $stSql .= "    ON(                                                                \n";
        $stSql .= "        CD.exercicio = tabela.exercicio  AND                           \n";
        $stSql .= "        CD.cod_conta = tabela.cod_conta                                \n";
        $stSql .= "    )                                                                  \n";
        $stSql .= "LEFT JOIN                                                              \n";
        $stSql .= "    orcamento.despesa AS D                                         \n";
        $stSql .= "    ON (                                                               \n";
        $stSql .= "        D.cod_despesa = tabela.cod_despesa AND                         \n";
        $stSql .= "        D.exercicio   = tabela.exercicio                               \n";
            $stSql .= "    )                                                                  \n";
        $stSql .= "WHERE                                                                  \n";
        $stSql .= "       tabela.num_orgao::varchar||tabela.num_unidade::varchar                            \n";
        $stSql .= "       IN (                                                            \n";
        $stSql .= "            SELECT                                                     \n";
        $stSql .= "                  num_orgao::varchar||num_unidade::varchar                               \n";
        $stSql .= "            FROM                                                       \n";
        $stSql .= "                 empenho.permissao_autorizacao                     \n";
        $stSql .= "            WHERE                                                      \n";
        $stSql .= "                 numcgm    = ".$this->getDado("numcgm")."     AND      \n";
        $stSql .= "                 exercicio =  '".$this->getDado("exercicio")."'        \n";
        $stSql .= "       )                                                               \n";

        return $stSql;
    }
    
    /**
        * Monta a cláusula SQL
        * Cada tipo de campo recebe aqui um tratamento diferenciado em seu retorno.
        * @access Public
        * @return String String contendo o SQL
    */
    public function montaRecuperaRelacionamentoTodosCompraLicitacao()
    {
       $stSql =  "
                    SELECT distinct tabela.*,                                                          
                           CD.cod_estrutural  AS cod_estrutural_conta                         
             FROM (                                                                 
                     SELECT  AE.cod_autorizacao,                                        
                             TO_CHAR(AE.dt_autorizacao,'dd/mm/yyyy') AS dt_autorizacao, 
                             PD.cod_despesa,                                            
                             D.cod_conta,                                               
                             CD.cod_estrutural AS cod_estrutural_rubrica,               
                             PE.descricao,                                              
                             PE.exercicio,                                              
                             PE.cod_pre_empenho,                                        
                             PE.cgm_beneficiario as credor,                             
                             AE.cod_entidade,                                           
                             AE.num_orgao,                                              
                             AE.num_unidade,                                            
                             AR.cod_reserva,                                            
                             C.nom_cgm as nom_fornecedor,                              
                         CASE WHEN O.anulada IS NOT NULL                                
                              THEN O.anulada                                                 
                              ELSE 'f'                                                       
                         END AS anulada
                             ,compra_direta.cod_modalidade AS compra_cod_modalidade
                             ,compra_direta.cod_compra_direta
                             ,adjudicacao.cod_modalidade AS licitacao_cod_modalidade
                             ,adjudicacao.cod_licitacao
                             ,item_pre_empenho.cod_centro AS centro_custo
             
                     FROM empenho.autorizacao_empenho AS AE
                     
                LEFT JOIN empenho.autorizacao_reserva AS AR                      
                       ON AR.exercicio       = AE.exercicio
                      AND AR.cod_entidade    = AE.cod_entidade
                      AND AR.cod_autorizacao = AE.cod_autorizacao               
                          
                LEFT JOIN empenho.autorizacao_anulada AS AA                        
                       ON AA.cod_autorizacao = AE.cod_autorizacao
                      AND AA.exercicio       = AE.exercicio
                      AND AA.cod_entidade    = AE.cod_entidade
                      
                LEFT JOIN orcamento.reserva AS  O                      
                       ON O.exercicio   = AR.exercicio
                      AND O.cod_reserva = AR.cod_reserva                        
                             
               INNER JOIN empenho.pre_empenho AS PE
                       ON AE.cod_pre_empenho = PE.cod_pre_empenho
                      AND AE.exercicio       = PE.exercicio
                             
               INNER JOIN sw_cgm AS  C
                       ON C.numcgm = PE.cgm_beneficiario             
             
                LEFT JOIN empenho.pre_empenho_despesa AS PD                      
                       ON PD.cod_pre_empenho = PE.cod_pre_empenho
                      AND PD.exercicio       = PE.exercicio                     
                
                LEFT JOIN orcamento.conta_despesa     AS CD                      
                       ON CD.exercicio = PD.exercicio
                      AND CD.cod_conta = PD.cod_conta                           
                          
                LEFT JOIN orcamento.despesa AS D                         
                       ON D.exercicio   = PD.exercicio
                      AND D.cod_despesa = PD.cod_despesa
             
             LEFT JOIN empenho.item_pre_empenho
                    ON item_pre_empenho.cod_pre_empenho = pe.cod_pre_empenho
                   AND item_pre_empenho.exercicio       = pe.exercicio
                   
             LEFT JOIN empenho.item_pre_empenho_julgamento
                    ON item_pre_empenho_julgamento.cod_pre_empenho  = item_pre_empenho.cod_pre_empenho   
                   AND item_pre_empenho_julgamento.exercicio        = item_pre_empenho.exercicio
                   AND item_pre_empenho_julgamento.num_item         = item_pre_empenho.num_item
             
             LEFT JOIN compras.julgamento_item
                    ON julgamento_item.exercicio      = item_pre_empenho_julgamento.exercicio_julgamento
                   AND julgamento_item.cod_cotacao    = item_pre_empenho_julgamento.cod_cotacao 
                   AND julgamento_item.cod_item       = item_pre_empenho_julgamento.cod_item
                   AND julgamento_item.lote           = item_pre_empenho_julgamento.lote
                   AND julgamento_item.cgm_fornecedor = item_pre_empenho_julgamento.cgm_fornecedor
             
             LEFT JOIN compras.cotacao_item
                    ON cotacao_item.exercicio   = julgamento_item.exercicio
                   AND cotacao_item.cod_cotacao = julgamento_item.cod_cotacao
                   AND cotacao_item.lote        = julgamento_item.lote
                   AND cotacao_item.cod_item    = julgamento_item.cod_item
             
             LEFT JOIN compras.cotacao
                    ON cotacao.cod_cotacao = cotacao_item.cod_cotacao
                   AND cotacao.exercicio   = cotacao_item.exercicio
             
             LEFT JOIN compras.mapa_cotacao
                    ON mapa_cotacao.cod_cotacao       = cotacao.cod_cotacao
                   AND mapa_cotacao.exercicio_cotacao = cotacao.exercicio
             
             LEFT JOIN compras.mapa
                    ON mapa.cod_mapa  = mapa_cotacao.cod_mapa
                   AND mapa.exercicio = mapa_cotacao.exercicio_mapa
             
             LEFT JOIN compras.compra_direta
                    ON compra_direta.cod_mapa       = mapa.cod_mapa
                   AND compra_direta.exercicio_mapa = mapa.exercicio
             
             LEFT JOIN licitacao.adjudicacao
                    ON adjudicacao.exercicio_cotacao = cotacao_item.exercicio 
                   AND adjudicacao.cod_cotacao       = cotacao_item.cod_cotacao
                   AND adjudicacao.lote              = cotacao_item.lote
                   AND adjudicacao.cod_item          = cotacao_item.cod_item 
             
                 WHERE AA.cod_autorizacao IS NULL
                     
                 ) AS tabela                                                            
             
        LEFT JOIN orcamento.conta_despesa AS CD                                  
               ON CD.exercicio = tabela.exercicio
              AND CD.cod_conta = tabela.cod_conta                                
                
        LEFT JOIN orcamento.despesa AS D                                         
               ON D.cod_despesa = tabela.cod_despesa
              AND D.exercicio   = tabela.exercicio                               
                                                                                      
            WHERE tabela.num_orgao::varchar||tabela.num_unidade::varchar                            
               IN ( SELECT num_orgao::varchar||num_unidade::varchar                               
                      FROM empenho.permissao_autorizacao                     
                     WHERE numcgm    = ".$this->getDado("numcgm")."
                       AND exercicio = '".$this->getDado("exercicio")."'        
                ) \n";
        return $stSql;
    }
    
    public function recuperaRelacionamentoTodosCompraLicitacao(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        if(trim($stOrdem))
            $stOrdem = (strpos($stOrdem,"ORDER BY") === false)?" ORDER BY $stOrdem":$stOrdem;
            
        $stSql = $this->montaRecuperaRelacionamentoTodosCompraLicitacao().$stCondicao.$stOrdem;
        $this->setDebug( $stSql );
        
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }
    
    public function montaRecuperaRelacionamentoReemitirAnulados()
    {
        $stSql  = "SELECT                                                   \n";
        $stSql .= "     ae.cod_autorizacao,                                 \n";
        $stSql .= "     ae.cod_pre_empenho,                                 \n";
        $stSql .= "     ae.cod_entidade,                                    \n";
        $stSql .= "     ae.exercicio,                                       \n";
        $stSql .= "     to_char(aa.dt_anulacao,'dd/mm/yyyy') as dt_anulacao,\n";
        $stSql .= "     pe.cgm_beneficiario,                                \n";
        $stSql .= "     sum(ipe.vl_total) as valor,                         \n";
        $stSql .= "     cg.nom_cgm                                          \n";
        $stSql .= "FROM                                                     \n";
        $stSql .= "     empenho.autorizacao_empenho as ae,              \n";
        $stSql .= "     empenho.autorizacao_anulada as aa,              \n";
        $stSql .= "     empenho.pre_empenho as pe                       \n";
        $stSql .= "LEFT JOIN                                                \n";
        $stSql .= "     empenho.pre_empenho_despesa as ped              \n";
        $stSql .= "ON                                                       \n";
        $stSql .= "         pe.cod_pre_empenho  = ped.cod_pre_empenho       \n";
        $stSql .= "     AND pe.exercicio        = ped.exercicio,            \n";
        $stSql .= "     empenho.item_pre_empenho as ipe,                \n";
        $stSql .= "     sw_cgm as cg                                       \n";
        $stSql .= "WHERE                                                    \n";
        $stSql .= "         ae.cod_autorizacao  = aa.cod_autorizacao        \n";
        $stSql .= "     AND ae.exercicio        = aa.exercicio              \n";
        $stSql .= "     AND ae.cod_entidade     = aa.cod_entidade           \n";
        $stSql .= "     AND ae.exercicio        = pe.exercicio              \n";
        $stSql .= "     AND ae.cod_pre_empenho  = pe.cod_pre_empenho        \n";
        $stSql .= "     AND pe.cgm_beneficiario = cg.numcgm                 \n";
        $stSql .= "     AND pe.cod_pre_empenho  = ipe.cod_pre_empenho       \n";
        $stSql .= "     AND pe.exercicio        = ipe.exercicio             \n";

        return $stSql;
    }

    /**
        * Executa um Select no banco de dados a partir do comando SQL
        * @access Public
        * @param  Object  $rsRecordSet Objeto RecordSet
        * @param  String  $stCondicao  String de condição do SQL (WHERE)
        * @param  Boolean $boTransacao
        * @return Object  Objeto Erro
    */
    public function recuperaRelacionamentoReemitirAnulados(&$rsRecordSet, $stCondicao = "", $stOrdem = "", $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        if(trim($stOrdem))
            $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
        $stGrupo  = " GROUP BY                               \n";
        $stGrupo .= "   to_char(aa.dt_anulacao,'dd/mm/yyyy'),\n";
        $stGrupo .= "   pe.cgm_beneficiario,                \n";
        $stGrupo .= "   cg.nom_cgm,                         \n";
        $stGrupo .= "   ae.cod_autorizacao,                 \n";
        $stGrupo .= "   ae.cod_pre_empenho,                 \n";
        $stGrupo .= "   ae.exercicio,                       \n";
        $stGrupo .= "   ae.cod_entidade                     \n";
        $stSql = $this->montaRecuperaRelacionamentoReemitirAnulados().$stCondicao.$stGrupo.$stOrdem;
        $this->setDebug( $stSql );
    //     $this->Debug();
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaMaiorDataAutorizacao()
    {
        $stSql  = "SELECT                                                                       \n";
        $stSql .= "    CASE WHEN max(dt_autorizacao) < to_date('01/01/".$this->getDado('stExercicio')."','dd/mm/yyyy') THEN  \n";
        $stSql .= "        '01/01/".$this->getDado('stExercicio')."'                                                         \n";
        $stSql .= "    ELSE                                                                     \n";
        $stSql .= "        to_char(max(dt_autorizacao),'dd/mm/yyyy')                            \n";
        $stSql .= "    END AS data_autorizacao                                                  \n";
        $stSql .= "FROM                                                                         \n";
        $stSql .= "    empenho.autorizacao_empenho                                              \n";

        return $stSql;

    }

    /**
        * Executa um Select no banco de dados a partir do comando SQL
        * @access Public
        * @param  Object  $rsRecordSet Objeto RecordSet
        * @param  String  $stCondicao  String de condição do SQL (WHERE)
        * @param  Boolean $boTransacao
        * @return Object  Objeto Erro
    */
    public function recuperaMaiorDataAutorizacao(&$rsRecordSet, $stCondicao = "", $stOrdem = "", $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        if(trim($stOrdem))
            $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
        $stSql = $this->montaRecuperaMaiorDataAutorizacao().$stCondicao.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    /**
        * Executa um Select no banco de dados a partir do comando SQL
        * @access Public
        * @param  Object  $rsRecordSet Objeto RecordSet
        * @param  String  $stCondicao  String de condição do SQL (WHERE)
        * @param  Boolean $boTransacao
        * @return Object  Objeto Erro
    */
    public function recuperaDadosAutorizacao(&$rsRecordSet, $stCondicao = "", $stOrdem = "", $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        if(trim($stOrdem))
            $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
        $stSql = $this->montaRecuperaDadosAutorizacao().$stCondicao.$stOrdem;
        $this->setDebug( $stSql );
    //     $this->Debug();
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaDadosAutorizacao()
    {
        $stSql  = "SELECT *                                                                     \n";
        $stSql .= "FROM                                                                         \n";
        $stSql .= "     empenho.autorizacao_empenho                                             \n";
        $stSql .= "WHERE                                                                        \n";
        $stSql .= "     cod_autorizacao = ".$this->getDado("cod_autorizacao")."  AND            \n";
        $stSql .= "     exercicio       = '".$this->getDado("exercicio")."'      AND            \n";
        $stSql .= "     cod_entidade    in (".$this->getDado("cod_entidade").")                    \n";

        return $stSql;

    }

    /**
        * Executa um Select no banco de dados a partir do comando SQL
        * @access Public
        * @param  Object  $rsRecordSet Objeto RecordSet
        * @param  String  $stCondicao  String de condição do SQL (WHERE)
        * @param  Boolean $boTransacao
        * @return Object  Objeto Erro
    */
    public function recuperaDadosAutorizacaoAnulada(&$rsRecordSet, $stCondicao = "", $stOrdem = "", $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        if(trim($stOrdem))
            $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
        $stSql = $this->montaRecuperaDadosAutorizacaoAnulada().$stCondicao.$stOrdem;
        $this->setDebug( $stSql );
    //     $this->Debug();
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaDadosAutorizacaoAnulada()
    {
        $stSql  = "SELECT aa.*                                                                  \n";
        $stSql .= "FROM                                                                         \n";
        $stSql .= "     empenho.autorizacao_empenho as ae,                                      \n";
        $stSql .= "     empenho.autorizacao_anulada as aa                                       \n";
        $stSql .= "WHERE                                                                        \n";
        $stSql .= "         ae.cod_autorizacao  = aa.cod_autorizacao                            \n";
        $stSql .= "     AND ae.exercicio        = aa.exercicio                                  \n";
        $stSql .= "     AND ae.cod_entidade     = aa.cod_entidade                               \n";
        $stSql .= "     AND aa.cod_autorizacao  = ".$this->getDado("cod_autorizacao")."         \n";
        $stSql .= "     AND aa.exercicio        = '".$this->getDado("exercicio")."'             \n";
        $stSql .= "     AND aa.cod_entidade     = ".$this->getDado("cod_entidade")."            \n";

        return $stSql;

    }

    public function recuperaSolicitacaoAutorizacao(&$rsRecordSet, $stCondicao = "", $stOrdem = "", $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        if(trim($stOrdem))
            $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
        $stSql = $this->montaRecuperaSolicitacaoAutorizacao().$stCondicao.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaSolicitacaoAutorizacao()
    {
        $stSql = " select solicitacao.cod_solicitacao
                        , solicitacao.observacao
                        , solicitacao.exercicio
                        , solicitacao.cod_almoxarifado
                        , solicitacao.cod_entidade
                        , solicitacao.cgm_solicitante
                        , solicitacao.cgm_requisitante
                        , solicitacao.cod_objeto
                        , solicitacao.prazo_entrega
                        , solicitacao.timestamp
                     from empenho.autorizacao_empenho

                   inner join empenho.pre_empenho
                           on pre_empenho.exercicio = autorizacao_empenho.exercicio
                          and pre_empenho.cod_pre_empenho = autorizacao_empenho.cod_pre_empenho

                   inner join empenho.item_pre_empenho
                           on pre_empenho.exercicio = item_pre_empenho.exercicio
                          and pre_empenho.cod_pre_empenho = item_pre_empenho.cod_pre_empenho

                   inner join empenho.item_pre_empenho_julgamento
                           on item_pre_empenho_julgamento.exercicio = item_pre_empenho.exercicio
                          and item_pre_empenho_julgamento.cod_pre_empenho = item_pre_empenho.cod_pre_empenho
                          and item_pre_empenho_julgamento.num_item = item_pre_empenho.num_item

                   inner join compras.julgamento_item
                           on julgamento_item.exercicio = item_pre_empenho_julgamento.exercicio
                          and julgamento_item.cod_cotacao = item_pre_empenho_julgamento.cod_cotacao
                          and julgamento_item.cod_item = item_pre_empenho_julgamento.cod_item
                          and julgamento_item.lote = item_pre_empenho_julgamento.lote
                          and julgamento_item.cgm_fornecedor = item_pre_empenho_julgamento.cgm_fornecedor

                   inner join compras.julgamento
                           on julgamento_item.exercicio   =	julgamento.exercicio
                          and julgamento_item.cod_cotacao = julgamento.cod_cotacao

                   inner join compras.cotacao
                           on cotacao.exercicio   = julgamento.exercicio
                          and cotacao.cod_cotacao = julgamento.cod_cotacao

                   inner join compras.mapa_cotacao
                           on cotacao.exercicio   =	mapa_cotacao.exercicio_cotacao
                          and cotacao.cod_cotacao = mapa_cotacao.cod_cotacao

                   inner join compras.mapa
                           on mapa.exercicio = mapa_cotacao.exercicio_mapa
                          and mapa.cod_mapa = mapa_cotacao.cod_mapa

                   inner join compras.mapa_solicitacao
                           on mapa_solicitacao.exercicio = 	mapa.exercicio
                          and mapa_solicitacao.cod_mapa = mapa.cod_mapa

                   inner join compras.solicitacao
                           on mapa_solicitacao.exercicio_solicitacao = 	solicitacao.exercicio
                          and mapa_solicitacao.cod_solicitacao = solicitacao.cod_solicitacao
                          and mapa_solicitacao.cod_entidade = solicitacao.cod_entidade

                        where autorizacao_empenho.cod_autorizacao is not null";

        if ($this->getDado('cod_autorizacao')) {
                $stSql.=" and autorizacao_empenho.cod_autorizacao =".$this->getDado('cod_autorizacao');
        }
        if ($this->getDado('exercicio')) {
                $stSql.=" and autorizacao_empenho.exercicio ='".$this->getDado('exercicio')."'";
        }
        if ($this->getDado('exercicio')) {
                $stSql.=" and autorizacao_empenho.cod_entidade =".$this->getDado('cod_entidade');
        }
        $stSql.="    group by solicitacao.cod_solicitacao
                            , solicitacao.observacao
                            , solicitacao.exercicio
                            , solicitacao.cod_almoxarifado
                            , solicitacao.cod_entidade
                            , solicitacao.cgm_solicitante
                            , solicitacao.cgm_requisitante
                            , solicitacao.cod_objeto
                            , solicitacao.prazo_entrega
                            , solicitacao.timestamp";

        return $stSql;
    }

    }
