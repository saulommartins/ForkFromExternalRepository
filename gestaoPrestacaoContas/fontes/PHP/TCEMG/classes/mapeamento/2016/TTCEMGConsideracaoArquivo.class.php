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
   /*
    * Classe de Mapeamento da tabela tcemg.consideracao_arquivo
    * Data de Criação   : 19/01/2009

    * @author Analista      Sergio Santos
    * @author Desenvolvedor Lisiane Morais

    * @package URBEM
    * @subpackage

    $Id: TTCEMGConsideracaoArquivo.class.php 62857 2015-06-30 13:53:56Z franver $
    */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CLA_PERSISTENTE                                                                      );

class TTCEMGConsideracaoArquivo extends Persistente
{
    public function TTCEMGConsideracaoArquivo()
    {
        parent::Persistente();
        $this->setTabela("tcemg.consideracao_arquivo");
        $this->setCampoCod('cod_arquivo');
        $this->setComplementoChave('');
        $this->AddCampo('cod_arquivo','integer',true,'',true,false);
        $this->AddCampo('nom_arquivo','varchar',true,'"15"',false,false);
    }

    public function recuperaDadosArquivo(&$rsRecordSet, $stFiltro, $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stOrdem = " ORDER BY consideracao_arquivo.cod_arquivo";
        $stSql = $this->montaRecuperaDadosArquivo().$stFiltro.$stOrdem;
        $this->stDebug = $stSql;
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaDadosArquivo()
    {
        $stSql  = "
            SELECT *
              FROM tcemg.consideracao_arquivo
              ";

        return $stSql;
    }

    public function recuperaConsid(&$rsRecordSet, $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql = $this->montaRecuperaConsid().$stFiltro.$stOrdem;
        $this->stDebug = $stSql;
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaConsid()
    {
        $stSql  = "
            SELECT 10 as tipo_registro
                  , 'FLPGO' AS nom_arquivo
                  , CAD.descricao as consideracoes
              FROM tcemg.consideracao_arquivo
              
              JOIN tcemg.consideracao_arquivo_descricao as CAD
                ON CAD.cod_arquivo = consideracao_arquivo.cod_arquivo
               AND CAD.descricao != ''
             
             WHERE CAD.periodo      = '".$this->getDado('mes')."'
               AND CAD.cod_entidade IN(".$this->getDado('entidade').")
               AND CAD.modulo_sicom = '".$this->getDado('modulo_sicom')."'
          ORDER BY consideracao_arquivo.cod_arquivo
        ";
        return $stSql;
    }

    public function __destruct(){}
}
?>
