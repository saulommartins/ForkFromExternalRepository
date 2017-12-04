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
  * Classe de mapeamento da tabela ECONOMICO.CADASTRO_ECONOMICO_EMPRESA_DIREITO
  * Data de Criação: 17/11/2004

  * @author Analista: Ricardo Lopes de Alencar
  * @author Desenvolvedor: Tonismar Régis Bernardo

  * @package URBEM
  * @subpackage Mapeamento

    * $Id: TCEMCadastroEconomicoEmpresaDireito.class.php 63839 2015-10-22 18:08:07Z franver $

* Casos de uso: uc-05.02.10
*/

/*
$Log$
Revision 1.11  2006/09/15 12:08:26  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

/**
  * Efetua conexão com a tabela  ECONOMICO.CADASTRO_ECONOMICO_EMPRESA_DIREITO
  * Data de Criação: 17/11/2004

  * @author Analista: Ricardo Lopes de Alencar
  * @author Desenvolvedor: Tonismar Régis Bernardo

  * @package URBEM
  * @subpackage Mapeamento
*/
class TCEMCadastroEconomicoEmpresaDireito extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TCEMCadastroEconomicoEmpresaDireito()
{
    parent::Persistente();
    $this->setTabela('economico.cadastro_economico_empresa_direito');

    $this->setCampoCod('inscricao_economica');
    $this->setComplementoChave('');

    $this->AddCampo('inscricao_economica','integer',true,'',true,true);
    $this->AddCampo('numcgm','integer',true,'',false,true);
    $this->AddCampo('cod_categoria','integer',true,'',false,true);
    $this->AddCampo('num_registro_junta','integer',false,'',false,false);

}

function recuperaInscricao(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaInscricao().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, "", $boTransacao );

    return $obErro;
}

function montaRecuperaInscricao()
{
    $stSql .= "
        SELECT DISTINCT ce.inscricao_economica
             , ed.num_registro_junta
             , ed.cod_categoria
             , substr(EDNJ.cod_natureza::varchar,'0',length(EDNJ.cod_natureza::varchar)) || '-'|| substr(EDNJ.cod_natureza::varchar,length(EDNJ.cod_natureza::varchar),'1') as cod_natureza
             , rp.numcgm as resp_numcgm
             , cgm.nom_cgm
             , cgm.numcgm
             , nj.nom_natureza
             , rp.sequencia
             , economico.fn_busca_sociedade(ce.inscricao_economica) as sociedade
          FROM economico.cadastro_economico as ce
    INNER JOIN ( SELECT rp.*
                   FROM economico.cadastro_econ_resp_contabil AS rp
             INNER JOIN ( SELECT MAX(timestamp) AS timestamp
                               , inscricao_economica
                            FROM economico.cadastro_econ_resp_contabil
                        GROUP BY inscricao_economica
                        )as tmp
                     ON tmp.inscricao_economica = rp.inscricao_economica
                    AND tmp.timestamp = rp.timestamp
               )as rp
            ON rp.inscricao_economica = ce.inscricao_economica
     LEFT JOIN ( SELECT tmp.*
                 FROM economico.baixa_cadastro_economico AS tmp
           INNER JOIN ( SELECT MAX(timestamp) AS timestamp
                             , inscricao_economica
                          FROM economico.baixa_cadastro_economico
                      GROUP BY inscricao_economica
                      )AS tmp2
                   ON tmp.timestamp = tmp2.timestamp
                  AND tmp.inscricao_economica = tmp2.inscricao_economica
               )as ba
            ON ce.inscricao_economica = ba.inscricao_economica
    INNER JOIN economico.cadastro_economico_empresa_direito as ed
            ON ed.inscricao_economica = ce.inscricao_economica
    INNER JOIN sw_cgm as cgm
            ON ed.numcgm = cgm.numcgm
    INNER JOIN ( SELECT tmp.*
                   FROM economico.empresa_direito_natureza_juridica AS tmp
             INNER JOIN ( SELECT max(timestamp) as timestamp
                               , inscricao_economica
                            FROM economico.empresa_direito_natureza_juridica
                        GROUP BY inscricao_economica
                        )AS tmp2
                     ON tmp.inscricao_economica = tmp2.inscricao_economica
                    AND tmp.timestamp = tmp2.timestamp
               )as ednj
            ON ce.inscricao_economica = ednj.inscricao_economica
    INNER JOIN economico.natureza_juridica as nj
            ON ednj.cod_natureza = nj.cod_natureza
         WHERE CASE WHEN ba.dt_inicio is not null AND ba.dt_termino is null THEN
                    false
               ELSE
                    true
               END ";

    return $stSql;
}

}
