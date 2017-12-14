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
    * Classe mapeamento tabela patrimonio.arquivo_coletora_consistencia
    *
    *
    * @date 18/08/2010
    * @author Analista: Gelson
    * @author Desenvol: Tonismar
    *
    * @ignore
**/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_FW_BANCO_DADOS."Transacao.class.php" );
include_once(CLA_PERSISTENTE);

class TPatrimonioArquivoColetoraConsistencia extends Persistente
{
    public function TPatrimonioArquivoColetoraConsistencia()
    {
        parent::Persistente();
        $this->setTabela('patrimonio.arquivo_coletora_consistencia');
        $this->setCampoCod('codigo');
        $this->setComplementoChave('num_placa');
        $this->addCampo('num_placa','varchar',true,'20',true,true);
        $this->addCampo('codigo','integer',true,'',true,true);
        $this->addCampo('status','varchar',true,'35',false,false);
        $this->addCampo('orientacao','varchar',false,'70',false,false);
        $this->transacao = new Transacao();
    }
}
