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
    * Classe de mapeamento do arquivo PESSOA.CSV
    * Data de Criação:  10/02/2014

    * @author Analista: Sergio
    * @author Desenvolvedor: Arthur Cruz

    * @package URBEM
    * @subpackage Mapeamento

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TTCEMGArquivoMensalPessoa extends Persistente
{
    public function TTCEMGArquivoMensalPessoa()
    {
        parent::Persistente();
        $this->setDado('exercicio', Sessao::getExercicio() );
    }

    public function recuperaDadosExportacaoMensal(&$rsRecordSet, $stOrdem = "", $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql = $this->montaRecuperaDadosExportacaoMensal().$stOrdem;
        $this->stDebug = $stSql;
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );
    
        return $obErro;
    } 
    
     public function criaTabelaPessoas(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        return $this->executaRecupera("montaCriaTabelaPessoas",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function montaCriaTabelaPessoas()
    {
        $stSql = "  INSERT INTO tcemg.arquivo_pessoa
                         SELECT sw_cgm.numcgm
                                , '".$this->getDado('exercicio')."' as ano
                                , '".$this->getDado('mes')."' as mes
                          FROM SW_CGM 
                     LEFT JOIN sw_cgm_pessoa_fisica 
                            ON SW_CGM.numcgm = sw_cgm_pessoa_fisica.numcgm 
                     LEFT JOIN sw_cgm_pessoa_juridica 
                            ON SW_CGM.numcgm = sw_cgm_pessoa_juridica.numcgm
                     LEFT JOIN sw_cga  
                            ON SW_CGM.numcgm = sw_cga.numcgm
                         WHERE SW_CGM.numcgm > 0
                           AND (sw_cgm_pessoa_fisica.cpf IS NOT NULL OR  sw_cgm_pessoa_juridica.cnpj IS NOT NULL )
                           AND (SW_CGM.numcgm NOT IN (SELECT numcgm FROM tcemg.arquivo_pessoa))
                      GROUP BY SW_CGM.numcgm
                             , sw_cgm_pessoa_fisica.cpf
                             , sw_cgm_pessoa_juridica.cnpj
                             , sw_cgm_pessoa_fisica.numcgm
                             , sw_cgm_pessoa_juridica.numcgm ";
        return $stSql;
    }

    public function montaRecuperaDadosExportacaoMensal()
    {
        $stSql = "
           SELECT
                  10 AS tipo_registro
                , SW_CGM.numcgm
                , sem_acentos(SW_CGM.nom_cgm) AS nome_razao_social
                , CASE WHEN sw_cgm_pessoa_fisica.cpf IS NOT NULL THEN sw_cgm_pessoa_fisica.cpf
                       ELSE sw_cgm_pessoa_juridica.cnpj END AS nro_documento 
                , CASE WHEN sw_cgm.cod_pais <> 1 THEN 3
                       WHEN sw_cgm_pessoa_fisica.cpf IS NOT NULL THEN 1
                       ELSE 2 END AS tipo_documento  
                , 1  AS tipo_cadastro 
                , '' AS justificativa_alteracao
           
           FROM SW_CGM 

      LEFT JOIN sw_cgm_pessoa_fisica
             ON SW_CGM.numcgm = sw_cgm_pessoa_fisica.numcgm 

      LEFT JOIN sw_cgm_pessoa_juridica
             ON SW_CGM.numcgm = sw_cgm_pessoa_juridica.numcgm 

      LEFT JOIN sw_cga
             ON SW_CGM.numcgm = sw_cga.numcgm
        
     INNER JOIN tcemg.arquivo_pessoa 
             ON SW_CGM.numcgm = arquivo_pessoa.numcgm
            AND arquivo_pessoa.mes = ".$this->getDado('mes')."
            AND arquivo_pessoa.ano = '".$this->getDado('exercicio')."'
            
          WHERE SW_CGM.numcgm > 0
            AND (sw_cgm_pessoa_fisica.cpf IS NOT NULL OR  sw_cgm_pessoa_juridica.cnpj IS NOT NULL )    

       GROUP BY SW_CGM.numcgm
              , sw_cgm_pessoa_fisica.cpf
              , sw_cgm_pessoa_juridica.cnpj
              , sw_cgm_pessoa_fisica.numcgm
              , sw_cgm_pessoa_juridica.numcgm ";
        
        return $stSql;
    }
    
    public function __destruct(){}

}

?>