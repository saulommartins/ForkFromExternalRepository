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
    * Classe de mapeamento da tabela siconfi.fn_relatorio_anexo_dca_ig
    * Data de Criação: 09/07/2015

    * @author Desenvolvedor: Franver Sarmento de Moraes

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 62933 $
    $Name$
    $Autor: $
    $Date: 2015-07-09 11:18:16 -0300 (Thu, 09 Jul 2015) $
    $Id: FSICONFIRelatorioAnexoDCAIG.class.php 62933 2015-07-09 14:18:16Z franver $
*/
class FSICONFIRelatorioAnexoDCAIG extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function FSICONFIRelatorioAnexoDCAIG()
    {
        parent::Persistente();
        $this->setTabela('siconfi.fn_relatorio_anexo_dca_ig');
    }
    
    function montaRecuperaTodos()
    {
        $stSql = "
          SELECT nivel
               , CASE WHEN cod_subfuncao = 0
                      THEN LPAD(cod_funcao::VARCHAR, 2, '0')
                      ELSE LPAD(cod_funcao::VARCHAR, 2, '0')||'.'||LPAD(cod_subfuncao::VARCHAR, 3, '0')
                  END AS funcao_subfuncao
               , descricao
               , to_real(vl_rp_nao_processados_pagos) AS vl_rp_nao_processados_pagos
               , to_real(vl_rp_nao_processados_cancelados) AS vl_rp_nao_processados_cancelados
               , to_real(vl_rp_processados_pagos) AS vl_rp_processados_pagos
               , to_real(vl_rp_processados_cancelados) AS vl_rp_processados_cancelados
            FROM ".$this->getTabela()."('".$this->getDado("exercicio")."','".$this->getDado("stEntidades")."','".$this->getDado("stDataFinal")."')
        ";
        
        return $stSql;
    }
}

?>