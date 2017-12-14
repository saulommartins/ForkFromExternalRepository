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
  * Mapeamento contabilidade.configuracao_contas_extras
  * Data de Criação: 05/11/2015
  * 
  * @author Analista      Valtair Santos
  * @author Desenvolvedor Franver Sarmento de Moraes
  *
  * $Id: TContabilidadeConfiguracaoContasExtras.class.php 63906 2015-11-05 12:31:01Z franver $
  * $Revision: 63906 $
  * $Author: franver $
  * $Date: 2015-11-05 10:31:01 -0200 (Thu, 05 Nov 2015) $
*/
require_once CLA_PERSISTENTE;

class TContabilidadeConfiguracaoContasExtras extends Persistente {
        /**
     * Método Construtor
     * @access Private
     */
    public function __construct()
    {
        parent::Persistente();
        $this->setTabela('contabilidade.configuracao_contas_extras');
        $this->setComplementoChave('exercicio, cod_conta');

        $this->AddCampo('exercicio', 'varchar',  true,   '4',  true,  true);
        $this->AddCampo('cod_conta', 'integer',  true,    '',  true,  true);
    }
    
    public function montaRecuperaRelacionamento()
    {
        $stSql = "
            SELECT plano_conta.cod_conta
                 , plano_conta.exercicio
                 , plano_conta.cod_estrutural
                 , plano_conta.nom_conta
              FROM contabilidade.configuracao_contas_extras
        INNER JOIN contabilidade.plano_conta
                ON plano_conta.cod_conta = configuracao_contas_extras.cod_conta
               AND plano_conta.exercicio = configuracao_contas_extras.exercicio
             WHERE plano_conta.exercicio = '".$this->getDado('exercicio')."'
        ";
        return $stSql;
    }
    
    public function __destruct(){}

}

?>