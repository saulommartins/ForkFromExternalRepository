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
    * Pacote de configuração do TCETO - Mapeamento tceto.plano_analitica_classificacao
    * Data de Criação   : 07/11/2014

    * @author Analista: Silvia Martins Silva
    * @author Desenvolvedor: Michel Teixeira
    * $Id: TTTOPlanoAnaliticaClassificacao.class.php 60671 2014-11-07 13:27:44Z michel $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TTTOPlanoAnaliticaClassificacao extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function TTTOPlanoAnaliticaClassificacao()
    {
        parent::Persistente();
        $this->setTabela("tceto.plano_analitica_classificacao");

        $this->setCampoCod('cod_plano');
        $this->setComplementoChave('exercicio,tipo');

        $this->AddCampo( 'cod_plano'        ,'integer' ,true  , ''   ,true  ,true  );
        $this->AddCampo( 'exercicio'        ,'char'    ,true  , '4'  ,true  ,true  );
        $this->AddCampo( 'cod_classificacao','integer' ,true  , ''   ,false ,false );
    }

    public function recuperaContaAnalitica(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        return $this->executaRecupera("montaRecuperaContaAnalitica",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function montaRecuperaContaAnalitica()
    {
        
        $stSQL  =" SELECT                                                   
                         plano_analitica.cod_plano,                                        
                         plano_conta.exercicio,                                        
                         plano_conta.cod_conta,                                        
                         plano_conta.nom_conta,                                        
                         plano_conta.cod_estrutural,                                  
                         plano_analitica_classificacao.cod_classificacao
                         
                     FROM                                                     
                         contabilidade.plano_conta
                         
                     JOIN                 
                          contabilidade.plano_analitica
                       ON plano_conta.cod_conta  = plano_analitica.cod_conta 
                      AND plano_conta.exercicio  = plano_analitica.exercicio    
                          
                LEFT JOIN                 
                          tceto.plano_analitica_classificacao
                       ON plano_analitica_classificacao.cod_plano = plano_analitica.cod_plano
                      AND plano_analitica_classificacao.exercicio = plano_analitica.exercicio
                   
                 WHERE                            
                        plano_conta.cod_conta  = plano_analitica.cod_conta 
                   AND plano_conta.exercicio  = plano_analitica.exercicio
        ";
        
        return $stSQL;
    }
}
