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
  * Página de Formulario de Configuração de Orgão
  * Data de Criação: 07/01/2014

  * @author Analista:      Eduardo Paculski Schitz
  * @author Desenvolvedor: Franver Sarmento de Moraes

  * @ignore

  $Id: TTCMGOConfigurarArquivoDMR.class.php 59612 2014-09-02 12:00:51Z gelson $
  $Date: 2014-09-02 09:00:51 -0300 (Tue, 02 Sep 2014) $
  $Author: gelson $
  $Rev: 59612 $
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

class TTCMGOConfigurarArquivoDMR extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function TTCMGOConfigurarArquivoDMR()
    {
        parent::Persistente();
        
        $this->setTabela('tcmgo.configuracao_arquivo_dmr');
        
        $this->setCampoCod('');
        $this->setComplementoChave('exercicio,cod_norma');
        
        $this->AddCampo('exercicio'       , 'varchar', true, '4',  true, false);
        $this->AddCampo('cod_norma'       , 'integer', true,  '',  true,  true);
        $this->AddCampo('cod_tipo_decreto', 'integer', true,  '', false, false);
        
    }
    
    public function recuperaDecretos(&$rsRecordSet)
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql = $this->montaRecuperaDecretos().$stFiltro.$stOrdem;
        $this->stDebug = $stSql;
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, "", $boTransacao );
    }
    
    public function montaRecuperaDecretos()
    {
        $stSql = "
            SELECT N.cod_norma
                 , N.cod_tipo_norma
                 , TN.nom_tipo_norma
                 , to_char( N.dt_publicacao, 'dd/mm/yyyy' )  as dt_publicacao
                 , to_char( N.dt_assinatura, 'dd/mm/yyyy' )  as dt_assinatura
                 , N.nom_norma
                 , N.descricao
                 , N.exercicio
                 , lpad(num_norma,6,'0') as num_norma
                 , link
                 , ( lpad(num_norma,6,'0')||'/'||N.exercicio ) as num_norma_exercicio
                 , ( SELECT cod_tipo_decreto FROM tcmgo.configuracao_arquivo_dmr WHERE exercicio = N.exercicio AND cod_norma = N.cod_norma) AS tipo_decreto
              FROM normas.norma AS N
              LEFT JOIN normas.tipo_norma AS TN
                ON TN.cod_tipo_norma = N.cod_tipo_norma
              JOIN normas.norma_data_termino AS n_ndt
                ON n_ndt.cod_norma = N.cod_norma
             WHERE N.cod_norma IS NOT NULL
               AND N.cod_tipo_norma = 2
               AND N.exercicio = '".$this->getDado('exercicio')."'
               --AND ((n_ndt.dt_termino BETWEEN '2014-01-01' AND '2014-01-31') OR n_ndt.dt_termino IS NULL OR n_ndt.dt_termino >= '2014-01-31')
             ORDER BY N.num_norma
                 , N.exercicio
        
        ";
        return $stSql;
    }
}

?>