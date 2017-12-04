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
 * Classe de visao de cheque
 *
 * @category    Urbem
 * @package     Tesouraria
 * @author      Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
 * @author      Henrique Boaventura <henrique.boaventura@cnm.org.br>
 * $Id:$
 */

 /**
 * Classe
 *
 * Descrição longa(se houver)...
 *
 * @category   Urbem
 * @package    PackageName
 * @author     Analista Fulano de Tal <fulano.tal@cnm.org.br>
 * @author     Desenvolvedor Fulano de Tal <fulano.tal@cnm.org.br>
 */

class VTesourariaCheque
{
    public $obModel;

    public function __construct($oModel)
    {
        $this->obController = $obModel;
    }

    public function incluir($arParam)
    {
        $this->obController->obRMONContaCorrente->obRMONAgencia->obRMONBanco->stNumBanco = $arParam['inCodBancoTxt'  ];
        $this->obController->obRMONContaCorrente->obRMONAgencia->stNumAgencia            = $arParam['stNumAgenciaTxt'];
        $this->obController->obRMONContaCorrente->stNumeroConta                          = $arParam['stContaCorrente'];
        $this->obController->stNumCheque                                                 = $arParam['stNumeroCheque' ];

        $obErro = $this->obController->addCheque();
        if (!$obErro->ocorreu()) {
            SistemaLegado::alertaAviso('FMManterCheque.php' . '?' . Sessao::getId() . '&stAcao='.$arParam['stAcao'], 'Cheque cadastrado com sucesso',$arParam['stAcao'],'aviso', Sessao::getId(), "../");
        } else {
            sistemaLegado::exibeAviso($obErro->getDescricao(), 'n_incluir', 'erro');
        }
    }
}
