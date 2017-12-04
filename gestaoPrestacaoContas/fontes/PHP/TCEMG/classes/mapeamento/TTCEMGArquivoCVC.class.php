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
  * Data de Criação: 23/04/2015

  * @author Analista:
  * @author Desenvolvedor: Franver Sarmento de Moraes

  * @ignore

  $Id: TTCEMGArquivoCVC.class.php 62355 2015-04-28 17:36:36Z franver $
  $Date: 2015-04-28 14:36:36 -0300 (Tue, 28 Apr 2015) $
  $Author: franver $
  $Rev: 62355 $
*/
class TTCEMGArquivoCVC extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function TTCEMGArquivoCVC()
    {
        parent::Persistente();
        $this->setTabela('tcemg.arquivo_cvc');
        $this->setCampoCod('');
        $this->setComplementoChave('exercicio,num_orgao,num_unidade,cod_veiculo');
        
        $this->AddCampo('exercicio'   , 'varchar',  true, '4',  true,  true);
        $this->AddCampo('cod_veiculo' , 'integer',  true,  '',  true,  true);
        $this->AddCampo('num_orgao'   , 'integer',  true,  '',  true,  true);
        $this->AddCampo('num_unidade' , 'integer',  true,  '',  true,  true);
        $this->AddCampo('mes'         , 'varchar', false, '2', false, false);
    }
}
?>